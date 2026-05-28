<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Exception\ValidationException;
use PuntoPost\Sdk\Http\HttpResponse;
use PuntoPost\Sdk\Tests\Mock\MockHttpClient;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\GetParcelTrackingQrRequest;
use PuntoPost\Sdk\V1\WebApi;

class WebApiTest extends TestCase
{
    private MockHttpClient $httpClient;
    private WebApi $sut;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->sut = new WebApi($this->httpClient, MockHttpClient::BASE_URL);
    }

    public function testGetParcelTrackingQrReturnsPngBinary(): void
    {
        $binary = "\x89PNG\r\n\x1a\nfake-png-bytes";
        $response = new HttpResponse(
            200,
            $binary,
            ['Content-Type' => 'image/png']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/web/v1/parcels/qr/MXT0000000001.png',
            'body' => null,
            'headers' => [
                'Accept' => 'image/png',
                PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE,
                PuntoPostClient::RUNTIME_HEADER_NAME => PHP_VERSION,
            ],
        ];

        $this->httpClient->queueResponse($response);

        $result = $this->sut->getParcelTrackingQr(new GetParcelTrackingQrRequest('MXT0000000001'));

        $this->assertSame($binary, $result);
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testGetParcelTrackingQrThrowsOnValidationError(): void
    {
        $response = new HttpResponse(
            400,
            json_encode(['type' => 'VALIDATION', 'title' => 'Invalid identifier', 'detail' => 'Identifier is not a valid parcel reference', 'instance' => 'parcel'], JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );

        $this->httpClient->queueResponse($response);

        $this->expectException(ValidationException::class);

        $this->sut->getParcelTrackingQr(new GetParcelTrackingQrRequest('UNKNOWN'));
    }
}
