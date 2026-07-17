<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * عميل توليد النصوص لـ Glottical AI (REST generateContent).
 */
class MuallimxAiClient
{
    /**
     * يعمل عند وجود مفتاح صالح. يُعطّل فقط إذا عُطّل التفعيل صراحةً في .env (قيمة false/0/off/no).
     */
    public function isConfigured(): bool
    {
        if (! filled(config('muallimx_ai.api_key'))) {
            return false;
        }

        if (app()->runningUnitTests()) {
            return (bool) config('muallimx_ai.enabled', false);
        }

        $raw = env('GEMINI_ENABLED');
        if ($raw === null || $raw === '') {
            return true;
        }

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * ترجمة أخطاء الـAPI إلى رسالة للمستخدم.
     */
    public function userFacingErrorMessage(\Throwable $e): string
    {
        $msg = strtolower((string) $e->getMessage());

        if (str_contains($msg, '429') || str_contains($msg, 'quota') || str_contains($msg, 'resource_exhausted')) {
            return __('student.full_ai_suite.muallimx_ai_error_quota');
        }

        if (str_contains($msg, '401') || str_contains($msg, '403') || str_contains($msg, 'api key not valid')) {
            return __('student.full_ai_suite.muallimx_ai_error_auth');
        }

        if (str_contains($msg, '404') || str_contains($msg, 'not found')) {
            return __('student.full_ai_suite.muallimx_ai_error_model');
        }

        if (config('app.debug')) {
            return __('student.full_ai_suite.muallimx_ai_error_debug', [
                'detail' => mb_substr((string) $e->getMessage(), 0, 400),
            ]);
        }

        return __('student.full_ai_suite.muallimx_ai_error_generic');
    }

    /**
     * @param  int|null  $maxOutputTokensOverride  عند الحاجة لمخرجات أطول (مثل ملف HTML كامل)
     *
     * @throws \RuntimeException عند فشل الشبكة أو الاستجابة أو عدم وجود نص في المخرجات
     */
    public function generateFromPrompt(string $prompt, ?int $maxOutputTokensOverride = null): string
    {
        $key = trim((string) config('muallimx_ai.api_key'));
        $model = (string) config('muallimx_ai.model', 'gemini-flash-latest');
        $base = rtrim((string) config('muallimx_ai.base_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $timeout = (int) config('muallimx_ai.http_timeout', 60);
        $maxOut = $maxOutputTokensOverride ?? (int) config('muallimx_ai.max_output_tokens', 8192);

        $url = "{$base}/models/{$model}:generateContent";

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->withQueryParameters(['key' => $key])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => $maxOut,
                ],
            ]);

        if (! $response->successful()) {
            $body = $response->json();
            $msg = is_array($body) ? (string) data_get($body, 'error.message', $response->body()) : $response->body();
            Log::warning('Glottical AI HTTP error', [
                'status' => $response->status(),
                'message' => mb_substr($msg, 0, 500),
            ]);

            throw new \RuntimeException('HTTP '.$response->status().': '.mb_substr($msg, 0, 200));
        }

        $json = $response->json();
        if (! is_array($json)) {
            throw new \RuntimeException('Invalid JSON response');
        }

        if (isset($json['error']['message'])) {
            throw new \RuntimeException((string) $json['error']['message']);
        }

        $finish = data_get($json, 'candidates.0.finishReason');
        if ($finish === 'SAFETY' || $finish === 'BLOCKLIST') {
            throw new \RuntimeException('Response blocked by safety filters.');
        }

        $text = $this->extractTextFromResponse($json);
        if ($text === '') {
            Log::warning('Glottical AI empty or unparsed candidates', [
                'finishReason' => $finish,
                'keys' => array_keys($json),
                'candidate_preview' => mb_substr(json_encode(data_get($json, 'candidates.0'), JSON_UNESCAPED_UNICODE), 0, 800),
            ]);

            throw new \RuntimeException('No text in model response.');
        }

        return $text;
    }

    /**
     * @param  array<string, mixed>  $json
     */
    private function extractTextFromResponse(array $json): string
    {
        $candidates = data_get($json, 'candidates');
        if (! is_array($candidates)) {
            return '';
        }

        foreach ($candidates as $candidate) {
            if (! is_array($candidate)) {
                continue;
            }
            $parts = data_get($candidate, 'content.parts');
            if (! is_array($parts)) {
                continue;
            }
            $chunks = [];
            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                    $chunks[] = $part['text'];
                }
            }
            $joined = trim(implode('', $chunks));
            if ($joined !== '') {
                return $joined;
            }
        }

        return '';
    }
}
