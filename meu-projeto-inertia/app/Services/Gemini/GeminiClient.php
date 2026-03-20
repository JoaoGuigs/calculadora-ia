<?php

namespace App\Services\Gemini;

use GuzzleHttp\Client as GuzzleClient;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;

class GeminiClient
{
    public function generate(string $prompt, string $model = 'models/gemini-2.5-flash', bool $disableSslVerify = true): string
    {
        $apiKey = config('gemini.api_key');

        $httpClient = new GuzzleClient([
            'verify' => ! $disableSslVerify ? true : false,
        ]);

        $client = \Gemini::factory()
            ->withApiKey($apiKey)
            ->withHttpClient($httpClient)
            ->make();

        $genModel = $client->generativeModel($model)
            ->withGenerationConfig(new GenerationConfig(
                responseMimeType: ResponseMimeType::APPLICATION_JSON,
            ));

        $result = $genModel->generateContent($prompt);

        return $result->text();
    }
}

