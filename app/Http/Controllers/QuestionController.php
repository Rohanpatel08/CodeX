<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QuestionController extends Controller
{

    public function getAllQuestions()
    {
        return Question::with('testCases')->get();
    }
    public function store(Request $request)
    {
        $question = Question::create($request->only(['title', 'description', 'input_format', 'output_format', 'constraints']));

        foreach ($request->test_cases as $tc) {
            $question->testCases()->create([
                'input_data' => $tc['input'],
                'expected_output' => $tc['output'],
                'is_hidden' => $tc['is_hidden'] ?? false,
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function generateRandomQuestions(Request $request)
    {
        $params = $request->all();
        $prompt = $this->createCodingPrompt($params);

        $response = Http::withHeaders($this->getHeaders())->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            "max_tokens" => 500,
            "temperature" => 0.9,
            "top_p" => 1.0,
        ]);

        $JsonResponse = $response->json();
        $cleaned = str_replace(["```json\n", "```", "\n"], '', $JsonResponse['choices'][0]['message']['content']);
        $response = json_decode(trim($cleaned));
        if (isset($response->questions[0])) {
            $testCasesData = array_map(function ($tc) {
                return [
                    'input' => $tc->input,
                    'expected_output' => $tc->expected_output,
                ];
            }, $response->questions[0]->test_cases);

            $question = Question::create([
                'type' => $response->questions[0]->type,
                'title' => $response->questions[0]->title,
                'description' => $response->questions[0]->question,
                'input_format' => $response->questions[0]->input_format,
                'output_format' => $response->questions[0]->output_format,
                'constraints' => $response->questions[0]->constraints,
            ]);
            $question->testCases()->createMany($testCasesData);
            $question = Question::latest()->first();
            if ($question) {
                $testCases = $question->testCases()->get();
                return response()->json(['status' => 'success', 'question' => $question, 'testCases' => $testCases]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No question found']);
            }
        } else {
            return response()->json(['status' => 'error', 'response' => $response]);
        }
    }

    protected function getHeaders(): array
    {
        $openAIApiKey = env('OPENAI_API_KEY');
        return [
            'Authorization' => 'Bearer ' . $openAIApiKey,
        ];
    }


    public function createCodingPrompt($params)
    {
        $prompt = "Generate Easy questions = %s, Medium questions = %s, Hard questions = %s.

        You are a highly skilled question generator specializing in creating unique and challenging interview questions tailored to specific skill sets. Your expertise lies in formulating questions that are not easily searchable on the internet, ensuring they test the true depth of knowledge in various fields.

        Your task is to generate a set of LeetCode-style interview questions based on the provided parameters.

        Please adhere strictly to these rules for each question:

        Focus only on pure programming language concepts and algorithms.

        No database, framework-specific, or system-level code allowed.


        Question Type: %s (only 'Coding' is allowed)

        Duration must be between 1 to 10 minutes per question.


        Each question must follow this strict JSON structure:

        {
        \"questions\": [
        {
        \"type\": \"Coding\",
        \"title\": \"<Question Title>\",
        \"question\": \"<Formatted markdown-style problem statement>\",
        \"test_cases\": [
        {
        \"input\": \"<input for test>\",
        \"expected_output\": \"<expected result>\"
        },
        {
        \"input\": \"<input for test>\",
        \"expected_output\": \"<expected result>\"
        },
        {
        \"input\": \"<input for test>\",
        \"expected_output\": \"<expected result>\"
        }
        ],
        \"input_format\": \"<input format>\",
        \"output_format\": \"<output format>\",
        \"constraints\": \"<constraints>\",
        \"difficulty\": \"<Easy|Medium|Hard>\",
        \"duration\": <number between 1 and 10>,
        \"Score\": <number between 1 and 5>,
        \"Skill\": \"<Single skill this question is based on>\",
        \"Compiler\": [<language ID(s) based on the skill>]
        }
        ]
        }

        ⚠️ Return a valid JSON object with no explanation or text outside the JSON.";

        return sprintf(
            $prompt,
            intval($params['easy_questions'] ?? 0),
            intval($params['medium_questions'] ?? 0),
            intval($params['hard_questions'] ?? 1),
            'Coding',
        );
    }
}
