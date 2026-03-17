<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Http;

interface HttpClientInterface
{
    /**
     * @param string               $method  HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string               $url     Full URL to request
     * @param array<string,string> $headers Key-value headers
     * @param string|null          $body    Raw request body
     */
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?string $body = null
    ): HttpResponse;
}
