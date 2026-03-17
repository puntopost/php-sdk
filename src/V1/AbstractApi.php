<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1;

use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;

abstract class AbstractApi
{
    protected HttpClientInterface $httpClient;
    protected string $baseUrl;
    protected ?string $token;

    public function __construct(HttpClientInterface $httpClient, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = null;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param array<string,string> $headers
     * @param array<string,mixed>  $body
     *
     * @throws PuntoPostException
     * @throws ValidationException
     */
    protected function post(string $path, array $body, array $headers = []): HttpResponse
    {
        return $this->sendRequest('POST', $path, $headers, $body);
    }

    /**
     * @param array<string,mixed>  $queryParams
     * @param array<string,string> $headers
     *
     * @throws PuntoPostException
     * @throws ValidationException
     */
    protected function get(string $path, array $queryParams = [], array $headers = []): HttpResponse
    {
        $url = $this->baseUrl . $path;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $merged = array_merge($this->defaultRequestHeaders(), $headers);

        $response = $this->httpClient->request('GET', $url, $merged, null);
        $this->handleError($response);

        return $response;
    }

    /**
     * @param array<string,string> $headers
     *
     * @throws PuntoPostException
     * @throws ValidationException
     */
    protected function put(string $path, array $headers = []): HttpResponse
    {
        return $this->sendRequest('PUT', $path, $headers, null);
    }

    /**
     * @param array<string,string> $headers
     *
     * @throws PuntoPostException
     * @throws ValidationException
     */
    protected function delete(string $path, array $headers = []): HttpResponse
    {
        return $this->sendRequest('DELETE', $path, $headers, null);
    }

    /**
     * @param array<string,string>     $headers
     * @param array<string,mixed>|null $body
     *
     * @throws PuntoPostException
     * @throws ValidationException
     */
    private function sendRequest(string $method, string $path, array $headers, ?array $body): HttpResponse
    {
        $url = $this->baseUrl . $path;

        $defaultHeaders = $this->defaultRequestHeaders();
        $encodedBody = null;
        if ($body !== null) {
            $encodedBody = json_encode($body, JSON_THROW_ON_ERROR);
            $defaultHeaders['Content-Type'] = 'application/json';
        }

        $merged = array_merge($defaultHeaders, $headers);
        $response = $this->httpClient->request($method, $url, $merged, $encodedBody);

        $this->handleError($response);

        return $response;
    }

    /**
     * @throws PuntoPostException
     * @throws ValidationException
     */
    protected function handleError(HttpResponse $response): void
    {
        if ($response->isSuccessful()) {
            return;
        }

        $statusCode = $response->getStatusCode();
        $rawBody = $response->getBody();

        if ($statusCode === 400) {
            $data = json_decode($rawBody, true);
            if (is_array($data) && isset($data['type']) && $data['type'] === 'VALIDATION') {
                throw ValidationException::fromResponse($statusCode, $rawBody);
            }
        }

        throw PuntoPostException::fromResponse($statusCode, $rawBody);
    }

    /**
     * @return array<string,mixed>
     */
    protected function decodeBody(HttpResponse $response): array
    {
        $data = json_decode($response->getBody(), true);

        return is_array($data) ? $data : [];
    }

    /**
     * @return array<string, string>
     */
    private function defaultRequestHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE,
            PuntoPostClient::CLIENT_PHP_VERSION_HEADER_NAME => PHP_VERSION,
        ];
    }
}
