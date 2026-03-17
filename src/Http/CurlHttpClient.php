<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Http;

use RuntimeException;

class CurlHttpClient implements HttpClientInterface
{
    private int $timeoutSeconds;

    public function __construct(int $timeoutSeconds = 30)
    {
        if (!extension_loaded('curl')) {
            throw new RuntimeException('The cURL extension is required to use CurlHttpClient.');
        }

        $this->timeoutSeconds = $timeoutSeconds;
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse {
        $ch = curl_init();

        $curlHeaders = [];
        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_HEADER => true,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($errno !== 0 || $raw === false) {
            throw new RuntimeException('cURL request failed: ' . $error, $errno);
        }

        $rawResponse = (string) $raw;
        $responseBody = substr($rawResponse, $headerSize);
        $rawHeaders = substr($rawResponse, 0, $headerSize);

        return new HttpResponse($httpCode, $responseBody, $this->parseHeaders($rawHeaders));
    }

    /**
     * @return array<string,string>
     */
    private function parseHeaders(string $raw): array
    {
        $headers = [];
        $lines = explode("\r\n", $raw);

        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $headers[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $headers;
    }
}
