<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\Http\HttpResponse;
use PuntoPost\Sdk\Tests\Mock\MockHttpClient;
use PuntoPost\Sdk\V1\AuthApi;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\LoginRequest;
use PuntoPost\Sdk\V1\Response\LoginResponse;

class AuthApiTest extends TestCase
{
    private MockHttpClient $httpClient;
    private AuthApi $sut;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->sut = new AuthApi($this->httpClient, 'https://api.example.com');
    }

    public function testLoginSuccess(): void
    {
        $request = new LoginRequest('user', 'pass');
        $response = new HttpResponse(
            200,
            json_encode([
                'token' => 'auto-stored-token',
                'expires_in' => 3600
            ], JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'POST',
            'url' => 'https://api.example.com/api/auth/v1/login',
            'body' => '{"username":"user","password":"pass"}',
            'headers' => [
                'Accept' => 'application/json',
                PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE,
                PuntoPostClient::RUNTIME_HEADER_NAME => PHP_VERSION,
                'Content-Type' => 'application/json',
            ]
        ];
        $expectedResponse = new LoginResponse('auto-stored-token', 3600);

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->login($request));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testLoginInvalidCredentials(): void
    {
        $rawBody = '{"type":"UNAUTHORIZED","title":"Invalid credentials","detail":"The provided credentials are invalid","instance":"authentication"}';
        $response = new HttpResponse(401, $rawBody, ['Content-Type' => 'application/json']);
        $expectedException = PuntoPostException::fromResponse(401, $rawBody);
        $request = new LoginRequest('user', 'wrong');

        $this->httpClient->queueResponse($response);
        $this->expectExceptionObject($expectedException);

        $this->sut->login($request);
    }

    public function testLoginEmptyCredentialsThrowsValidationException(): void
    {
        $rawBody = '{"type":"VALIDATION","title":"Invalid parameters","detail":"There are the following validation errors.","instance":"request","errors":{"username":["The username \"\" cannot be empty."],"password":["The password \"\" cannot be empty."]}}';
        $response = new HttpResponse(400, $rawBody, ['Content-Type' => 'application/json']);
        $expectedException = ValidationException::fromResponse(400, $rawBody);
        $request = new LoginRequest('', '');

        $this->httpClient->queueResponse($response);
        $this->expectExceptionObject($expectedException);

        $this->sut->login($request);
    }
}
