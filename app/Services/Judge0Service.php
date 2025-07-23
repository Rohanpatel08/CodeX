<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Judge0Service
{
    private $apiUrl;
    private $apiKey;
    private $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.judge0.api_url');
        $this->apiKey = config('services.judge0.api_key');
        $this->timeout = config('services.judge0.timeout');
    }

    public function getLanguageId($language)
    {
        $languageMap = [
            'javascript' => 63,  // Node.js
            'python' => 71,      // Python 3
            'php' => 68,         // PHP
            'java' => 62,        // Java
            'cpp' => 54,         // C++
            'c' => 50,           // C
        ];

        return $languageMap[$language] ?? null;
    }

    public function executeCode($sourceCode, $language, $input = null)
    {
        $languageId = (int) $language;

        $payload = [
            'source_code' => $sourceCode,
            'language_id' => $languageId,
        ];

        if ($input) {
            $payload['stdin'] = $input;
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['X-RapidAPI-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->post($this->apiUrl . '/submissions?wait=true', $payload);

            if (!$response->successful()) {
                throw new \Exception('Judge0 API request failed: ' . $response->body());
            }

            return $this->formatResponse($response->json());
        } catch (\Exception $e) {
            Log::error('Judge0 API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function submitCode($sourceCode, $language, $input = null)
    {
        $languageId = $this->getLanguageId($language);
        
        if (!$languageId) {
            throw new \Exception("Unsupported language: $language");
        }

        $payload = [
            'source_code' => $sourceCode,
            'language_id' => $languageId,
        ];

        if ($input) {
            $payload['stdin'] = $input;
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['X-RapidAPI-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->post($this->apiUrl . '/submissions', $payload);

            if (!$response->successful()) {
                throw new \Exception('Judge0 API request failed: ' . $response->body());
            }

            return $response->json()['token'];
        } catch (\Exception $e) {
            Log::error('Judge0 API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getSubmissionResult($token)
    {
        $headers = [];
        
        if ($this->apiKey) {
            $headers['X-RapidAPI-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->get($this->apiUrl . '/submissions/' . $token);

            if (!$response->successful()) {
                throw new \Exception('Judge0 API request failed: ' . $response->body());
            }

            return $this->formatResponse($response->json());
        } catch (\Exception $e) {
            Log::error('Judge0 API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function formatResponse($response)
    {
        $status = $response['status']['description'] ?? 'Unknown';
        $output = '';
        $error = '';

        if (isset($response['stdout']) && !empty($response['stdout'])) {
            Log::info('Judge0 Raw stdout: ' . $response['stdout']);
            $decoded = base64_decode($response['stdout'], true);
            if ($decoded === false) {
                Log::warning('Base64 decode failed for stdout, using raw value');
                $output = $response['stdout'];
            } else {
                Log::info('Judge0 Decoded stdout: ' . $decoded);
                $output = $decoded;
            }
        }

        if (isset($response['stderr']) && !empty($response['stderr'])) {
            $decoded = base64_decode($response['stderr'], true);
            $error = $decoded !== false ? $decoded : $response['stderr'];
        }

        if (isset($response['compile_output']) && !empty($response['compile_output'])) {
            $decoded = base64_decode($response['compile_output'], true);
            $error = $decoded !== false ? $decoded : $response['compile_output'];
        }

        return [
            'status' => $status,
            'output' => $output ?: ($error ?: 'No output'),
            'error' => $error,
            'time' => $response['time'] ?? null,
            'memory' => $response['memory'] ?? null,
            'exit_code' => $response['exit_code'] ?? null,
            'token' => $response['token'] ?? null,
        ];
    }

    public function getSupportedLanguages()
    {
        $headers = [];
        
        if ($this->apiKey) {
            $headers['X-RapidAPI-Key'] = $this->apiKey;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout($this->timeout)
                ->get($this->apiUrl . '/languages');

            if (!$response->successful()) {
                throw new \Exception('Judge0 API request failed: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Judge0 API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}