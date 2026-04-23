<?php
declare(strict_types=1);

final class MeshokClient {
    private string $baseUrl;
    private string $apiKey;

    public function __construct(string $apiKey, string $baseUrl = 'https://meshok.net/sAPIv2') {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * @return array
     */
    public function post(string $method, array $payload = []): array {
        $url = $this->baseUrl . '/' . ltrim($method, '/');
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) $body = '{}';

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json',
            'User-Agent: AntikvarCRM/1.0 (+https://krimfasad.ru)',
        ];

        $raw = null;
        $httpCode = 0;

        // Prefer cURL when available, but hosting may not have it enabled.
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $body,
            ]);

            $raw = curl_exec($ch);
            $err = curl_error($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($raw === false) {
                throw new RuntimeException("Meshok request failed (cURL): {$err}");
            }
        } else {
            $ctx = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => $body,
                    'timeout' => 30,
                    'ignore_errors' => true, // allow reading body on non-200
                ]
            ]);
            $raw = @file_get_contents($url, false, $ctx);
            if ($raw === false) {
                $last = error_get_last();
                throw new RuntimeException("Meshok request failed (streams): " . ($last['message'] ?? 'unknown'));
            }
            // Parse HTTP status from $http_response_header if present
            if (isset($http_response_header) && is_array($http_response_header) && isset($http_response_header[0])) {
                if (preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
                    $httpCode = (int)$m[1];
                }
            }
        }

        $rawTrim = ltrim((string)$raw);
        $isHtml = (stripos($rawTrim, '<!doctype html') === 0) || (stripos($rawTrim, '<html') === 0);
        $looksLikeCloudflare = stripos($rawTrim, 'Just a moment') !== false
            || stripos($rawTrim, 'cf-browser-verification') !== false
            || stripos($rawTrim, 'cloudflare') !== false;

        if ($isHtml && $looksLikeCloudflare) {
            throw new RuntimeException(
                'Meshok/Cloudflare blocked server-to-server request (HTTP ' . $httpCode . '). ' .
                'Need IP whitelist or API access permission from hosting IP.'
            );
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException("Meshok invalid JSON (HTTP {$httpCode}): " . mb_substr($raw, 0, 500));
        }

        // API uses success=1 for OK, negative for error
        if ($httpCode >= 400) {
            $msg = isset($decoded['error']) ? (string)$decoded['error'] : ("HTTP {$httpCode}");
            throw new RuntimeException("Meshok HTTP error: {$msg}");
        }

        return $decoded;
    }
}

