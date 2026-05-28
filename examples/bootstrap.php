<?php

declare(strict_types=1);

// Shared bootstrap for all example scripts under examples/.
// Reads PP_HOST / PP_USERNAME / PP_PASSWORD from the environment and returns a
// logged-in PuntoPostClient. Endpoint-specific scripts get their own env vars on
// top (PP_MERCHANT_ID, etc.) via env_required() / env_optional().

require __DIR__ . '/../vendor/autoload.php';

use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\LoginRequest;

/**
 * Curl client that skips TLS verification — handy for `https://localhost`
 * setups with self-signed certs. Never use it outside of these example scripts.
 */
final class InsecureCurlHttpClient implements HttpClientInterface
{
    public function request(string $method, string $url, array $headers = [], ?string $body = null): HttpResponse
    {
        $ch = curl_init();
        $curlHeaders = [];
        foreach ($headers as $name => $value) {
            $curlHeaders[] = $name . ': ' . $value;
        }
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => $curlHeaders,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('cURL request failed: ' . $error);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        $responseHeaders = [];
        foreach (explode("\r\n", substr((string) $raw, 0, $headerSize)) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $responseHeaders[trim($parts[0])] = trim($parts[1]);
            }
        }

        return new HttpResponse($status, substr((string) $raw, $headerSize), $responseHeaders);
    }
}

function env_required(string $name): string
{
    $value = getenv($name);
    if ($value === false || $value === '') {
        fwrite(STDERR, "ERROR: missing required env var {$name}\n");
        exit(1);
    }

    return $value;
}

function env_optional(string $name, ?string $default = null): ?string
{
    $value = getenv($name);

    return ($value === false || $value === '') ? $default : $value;
}

function create_client(): PuntoPostClient
{
    $host = env_optional('PP_HOST', 'https://localhost');
    $username = env_required('PP_USERNAME');
    $password = env_required('PP_PASSWORD');

    $client = new PuntoPostClient($host, new InsecureCurlHttpClient());
    $loginResponse = $client->auth()->login(new LoginRequest($username, $password));
    $client->setToken($loginResponse->getToken());
    fwrite(STDERR, "[bootstrap] Logged in as {$username} on {$host}\n");

    return $client;
}

function dump_json($value): void
{
    echo json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

/**
 * Walks any value recursively (objects, arrays, DateTime, scalars) and
 * pretty-prints it as JSON. Reads private/protected object properties via
 * reflection so you can dump SDK response DTOs without writing mappers.
 *
 * @param mixed $value
 */
function dump_pretty($value): void
{
    echo json_encode(to_plain($value), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

/**
 * @param mixed $value
 * @return mixed
 */
function to_plain($value)
{
    if ($value instanceof DateTimeInterface) {
        return $value->format(DATE_ATOM);
    }
    if (is_object($value)) {
        $plain = [];
        $ref = new ReflectionObject($value);
        foreach ($ref->getProperties() as $prop) {
            $prop->setAccessible(true);
            $plain[$prop->getName()] = to_plain($prop->getValue($value));
        }

        return $plain;
    }
    if (is_array($value)) {
        return array_map('to_plain', $value);
    }

    return $value;
}

function prompt(string $label, ?string $default = null): string
{
    $hint = $default !== null ? " [{$default}]" : '';
    fwrite(STDERR, "{$label}{$hint}: ");
    $input = fgets(STDIN);
    $input = $input === false ? '' : trim($input);
    if ($input === '') {
        if ($default !== null) {
            return $default;
        }
        fwrite(STDERR, "ERROR: value required\n");
        exit(1);
    }

    return $input;
}

function prompt_optional(string $label, ?string $default = null): ?string
{
    $hint = $default !== null ? " [{$default}]" : ' (optional, press Enter to skip)';
    fwrite(STDERR, "{$label}{$hint}: ");
    $input = fgets(STDIN);
    $input = $input === false ? '' : trim($input);
    if ($input === '') {
        return $default;
    }

    return $input;
}
