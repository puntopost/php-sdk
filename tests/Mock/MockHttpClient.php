<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Mock;

use PuntoPost\Sdk\Http\HttpClientInterface;
use PuntoPost\Sdk\Http\HttpResponse;
use RuntimeException;

class MockHttpClient implements HttpClientInterface
{
    /** @var HttpResponse[] */
    private array $queue = [];

    /** @var array<int,array{method:string,url:string,headers:array<string,string>,body:string|null}> */
    private array $recordedRequests = [];

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
        $this->recordedRequests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];

        if (empty($this->queue)) {
            throw new RuntimeException('MockHttpClient: no more responses queued.');
        }

        return array_shift($this->queue);
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
}
