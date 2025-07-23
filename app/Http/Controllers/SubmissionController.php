<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Submission;
use App\Services\Judge0Service;

class SubmissionController extends Controller
{
    protected $judge0Service;

    public function __construct(Judge0Service $judge0Service)
    {
        $this->judge0Service = $judge0Service;
    }
    public function index()
    {
        $languages = $this->judge0Service->getSupportedLanguages();
        $questions = Question::all();
        // dd($languages);
        return view('welcome', compact('languages', 'questions'));
    }

    public function evaluate(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'language' => 'required|string',
            'question_id' => 'nullable',
        ]);
        $code = $validated['code'];
        $language = $validated['language'];
        $questionId = $validated['question_id'] ?? null;

        try {
            // Get question and test cases if question_id is provided
            $question = null;
            $testResults = [];

            if ($questionId && is_numeric($questionId)) {
                $question = Question::with('testCases')->find($questionId);
                if ($question) {
                    $testResults = $this->runTestCases($code, $language, $question->testCases);
                }
            }

            // Execute the code using Judge0
            $result = $this->judge0Service->executeCode($code, $language);
            $output = $result['output'];

            // Save submission to database
            $submission = new Submission();
            $submission->question_id = $questionId;
            $submission->code = $code;
            $submission->language = $language;
            $submission->output = $result['output'];
            $submission->status = $result['status'];
            $submission->execution_time = $result['time'];
            $submission->memory_usage = $result['memory'];
            $submission->save();

            return response()->json([
                'success' => true,
                'output' => $output,
                'test_results' => $testResults,
                'submission_id' => $submission->id,
                'execution_details' => [
                    'status' => $result['status'],
                    'time' => $result['time'],
                    'memory' => $result['memory'],
                    'exit_code' => $result['exit_code']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSupportedLanguages()
    {
        try {
            $languages = $this->judge0Service->getSupportedLanguages();
            return response()->json([
                'success' => true,
                'languages' => $languages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function runTestCases($code, $language, $testCases)
    {
        $results = [];
        foreach ($testCases as $testCase) {
            try {
                // Run the code with test case input via stdin
                $result = $this->judge0Service->executeCode($code, $language, $testCase->input_data);
                $actualOutput = str_replace(' ', '', trim($result['output']));
                $expectedOutput = str_replace(' ', '', trim($testCase->expected_output));

                $passed = ($actualOutput === $expectedOutput) && ($result['status'] === 'Accepted');
                $results[] = [
                    'input' => $testCase->input_data,
                    'expected' => $expectedOutput,
                    'actual' => $actualOutput,
                    'passed' => $passed,
                    'is_hidden' => $testCase->is_hidden,
                    'execution_details' => [
                        'status' => $result['status'],
                        'time' => $result['time'],
                        'memory' => $result['memory'],
                        'error' => $result['error']
                    ]
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'input' => $testCase->input_data,
                    'expected' => $testCase->expected_output,
                    'actual' => 'Error: ' . $e->getMessage(),
                    'passed' => false,
                    'is_hidden' => $testCase->is_hidden
                ];
            }
        }

        return $results;
    }
}
