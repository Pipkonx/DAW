<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $model = 'gemini-3.1-flash-lite-preview';
    protected $apiService;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct(ApiService $apiService)
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
        $this->apiService = $apiService;
    }

    /**
     * Generates content using the Gemini API.
     * 
     * @param string $prompt
     * @return string|null
     */
    public function generateContent(string $prompt)
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key is missing.');
            return "Error: Gemini API Key is missing. Please add GEMINI_API_KEY to your .env file.";
        }

        $maxRetries = 3;
        $retryCount = 0;
        $waitSeconds = 5;

        while ($retryCount <= $maxRetries) {
            try {
                $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
                
                $response = Http::withOptions(['http_errors' => false])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($url, [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 2048,
                        ]
                    ]);

                $statusCode = $response->status();

                // Advanced Cascading Fallback for 404 (Not Found)
                if ($statusCode === 404) {
                    if ($this->model === 'gemini-3.1-flash-lite-preview') {
                        Log::warning("Gemini 3.1 not found. Falling back to 1.5-flash-8b.");
                        $this->model = 'gemini-1.5-flash-8b';
                        continue;
                    } elseif ($this->model === 'gemini-1.5-flash-8b') {
                        Log::warning("Gemini 1.5-8b not found. Falling back to 1.5-flash.");
                        $this->model = 'gemini-1.5-flash';
                        continue;
                    }
                }

                if ($statusCode === 429 && $retryCount < $maxRetries) {
                    $retryCount++;
                    Log::warning("Gemini 429 detected (Quota). Retry $retryCount/$maxRetries. Waiting $waitSeconds seconds.");
                    sleep($waitSeconds);
                    $waitSeconds *= 2; 
                    continue;
                }

                if ($response->successful()) {
                    $this->apiService->trackRequest('Gemini');
                    $data = $response->json();
                    return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI.';
                }

                Log::error("Gemini API Error ($statusCode): " . $response->body());
                return "Error al comunicarse con la IA ($statusCode). Por favor, espera un momento.";

            } catch (\Exception $e) {
                if ($retryCount < $maxRetries) {
                    $retryCount++;
                    sleep($waitSeconds);
                    $waitSeconds *= 2;
                    continue;
                }
                Log::error('Gemini Service Exception: ' . $e->getMessage());
                return 'Excepción en el servicio de IA: ' . $e->getMessage();
            }
        }
    }

    /**
     * Streams content generation from the Gemini API.
     * 
     * @param string $prompt
     * @param callable $onChunk (function that receives the text chunk)
     */
    public function streamGenerateContent(string $prompt, callable $onChunk)
    {
        if (empty($this->apiKey)) {
            $onChunk("Error: Gemini API Key is missing.");
            return;
        }

        $maxRetries = 3;
        $retryCount = 0;
        $waitSeconds = 10;

        while ($retryCount <= $maxRetries) {
            $url = $this->baseUrl . $this->model . ':streamGenerateContent?alt=sse&key=' . $this->apiKey;

            try {
                $client = new \GuzzleHttp\Client(['http_errors' => false]);
                $response = $client->post($url, [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['temperature' => 0.7, 'maxOutputTokens' => 2048]
                    ],
                    'stream' => true,
                ]);

                $statusCode = $response->getStatusCode();

                // Streaming Cascading Fallback for 404
                if ($statusCode === 404) {
                    if ($this->model === 'gemini-3.1-flash-lite-preview') {
                        Log::warning("Gemini Stream 3.1 not found. Falling back to 1.5-flash-8b.");
                        $this->model = 'gemini-1.5-flash-8b';
                        continue;
                    } elseif ($this->model === 'gemini-1.5-flash-8b') {
                        Log::warning("Gemini Stream 1.5-8b not found. Falling back to 1.5-flash.");
                        $this->model = 'gemini-1.5-flash';
                        continue;
                    }
                }

                if ($statusCode === 429 && $retryCount < $maxRetries) {
                    $retryCount++;
                    Log::warning("Gemini 429 detected (Quota). Retry $retryCount/$maxRetries. Waiting $waitSeconds seconds.");
                    sleep($waitSeconds);
                    $waitSeconds *= 2;
                    continue;
                }

                if ($statusCode !== 200) {
                    $body = $response->getBody()->getContents();
                    Log::error("Gemini API Error ($statusCode): " . $body);
                    $onChunk("Error al comunicarse con la IA: $statusCode. Por favor, espera un minuto e intenta de nuevo.");
                    return;
                }

                $this->apiService->trackRequest('Gemini');

                $body = $response->getBody();
                $buffer = '';

                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    $buffer .= $chunk;

                    while (($pos = strpos($buffer, "\n\n")) !== false) {
                        $event = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 2);

                        if (strpos($event, 'data: ') === 0) {
                            $jsonData = substr($event, 6);
                            if (trim($jsonData) === '[DONE]') break 2; // Exit both loops

                            $data = json_decode($jsonData, true);
                            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                                $onChunk($data['candidates'][0]['content']['parts'][0]['text']);
                            }
                        }
                    }
                }
                return; // Success, exit while

            } catch (\Exception $e) {
                if ($retryCount < $maxRetries) {
                    $retryCount++;
                    sleep($waitSeconds);
                    $waitSeconds *= 2;
                    continue;
                }
                Log::error('Gemini Streaming Exception: ' . $e->getMessage());
                $onChunk('Excepción en el streaming de IA: ' . $e->getMessage());
                return;
            }
        }
    }
}
