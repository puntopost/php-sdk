<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Mock;

use InvalidArgumentException;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\ServerRequestValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use RuntimeException;

class MockHttpClient implements HttpClientInterface
{
    public const BASE_URL = 'https://api.example.com';
    private const OPENAPI_SPEC_PATH = __DIR__ . '/../fixtures/openapi.json';

    /** @var HttpResponse[] */
    private array $queue;

    /** @var array<int,array{method:string,url:string,headers:array<string,string>,body:string|null}> */
    private array $recordedRequests;

    private ServerRequestValidator $requestValidator;
    private ResponseValidator $responseValidator;

    public function __construct()
    {
        /** @var array<string,mixed> $spec */
        $spec = json_decode((string)file_get_contents(self::OPENAPI_SPEC_PATH), true, 512, JSON_THROW_ON_ERROR);
        $spec['servers'] = [['url' => self::BASE_URL]];

        // Strip global security — we validate structure, not auth headers
        unset($spec['security']);

        // Forbid extra properties in all schemas so mock data must match exactly
        /** @var array<string,mixed> $components */
        $components = $spec['components'];
        /** @var array<string,array<string,mixed>> $schemas */
        $schemas = $components['schemas'];
        foreach ($schemas as $name => $schema) {
            if (($schema['type'] ?? null) === 'object' && isset($schema['properties'])) {
                $schemas[$name]['additionalProperties'] = false;
            }
        }
        $components['schemas'] = $schemas;
        $spec['components'] = $components;

        $json = json_encode($spec, JSON_THROW_ON_ERROR);

        $builder = (new ValidatorBuilder())->fromJson($json);
        $this->requestValidator = $builder->getServerRequestValidator();
        $this->responseValidator = $builder->getResponseValidator();
        $this->queue = [];
        $this->recordedRequests = [];
    }

    public function queueResponse(HttpResponse $response): void
    {
        $this->queue[] = $response;
    }

    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse {
        $psrRequest = $this->buildPsr7Request($method, $url, $headers, $body);

        try {
            $matched = $this->requestValidator->validate($psrRequest);
        } catch (ValidationFailed $e) {
            throw new InvalidArgumentException(
                $this->formatError('Request', $method, $url, $e),
                0,
                $e
            );
        }

        $this->recordedRequests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];

        if (empty($this->queue)) {
            throw new RuntimeException('MockHttpClient: no more responses queued.');
        }

        $httpResponse = array_shift($this->queue);

        $psrResponse = $this->buildPsr7Response($httpResponse);
        $operation = new OperationAddress($matched->path(), $matched->method());

        try {
            $this->responseValidator->validate($operation, $psrResponse);
        } catch (ValidationFailed $e) {
            throw new InvalidArgumentException(
                $this->formatError('Response', $method, $url, $e),
                0,
                $e
            );
        }

        return $httpResponse;
    }

    /**
     * @return array{method:string,url:string,headers:array<string,string>,body:string|null}|null
     */
    public function getLastRequest(): ?array
    {
        if (empty($this->recordedRequests)) {
            return null;
        }

        return end($this->recordedRequests);
    }

    public function getRequestCount(): int
    {
        return count($this->recordedRequests);
    }

    /**
     * @param array<string,string> $headers
     */
    private function buildPsr7Request(string $method, string $url, array $headers, ?string $body): ServerRequest
    {
        $request = new ServerRequest(strtoupper($method), $url);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body !== null) {
            $stream = (new Psr17Factory())->createStream($body);
            $request = $request->withBody($stream);
        }

        return $request;
    }

    private function buildPsr7Response(HttpResponse $httpResponse): Response
    {
        $response = new Response(
            $httpResponse->getStatusCode(),
            [],
            (new Psr17Factory())->createStream($httpResponse->getBody())
        );

        foreach ($httpResponse->getHeaders() as $name => $value) {
            $response = $response->withHeader($name, is_array($value) ? $value : [$value]);
        }

        return $response;
    }

    private function formatError(string $phase, string $method, string $url, ValidationFailed $e): string
    {
        $messages = [];
        $current = $e;
        while ($current !== null) {
            $messages[] = $current->getMessage();
            $current = $current->getPrevious();
        }

        return sprintf(
            "%s validation failed for %s %s:\n  %s",
            $phase,
            strtoupper($method),
            $url,
            implode("\n  <- ", $messages)
        );
    }
}
