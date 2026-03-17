<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Exception\PuntoPostException;
use PuntoPost\Sdk\Http\HttpResponse;
use PuntoPost\Sdk\Tests\Mock\MockHttpClient;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\V1\MerchantApi;
use PuntoPost\Sdk\V1\PuntoPostClient;
use PuntoPost\Sdk\V1\Request\CancelParcelRequest;
use PuntoPost\Sdk\V1\Request\CheckCoverageRequest;
use PuntoPost\Sdk\V1\Request\CreateB2CParcelRequest;
use PuntoPost\Sdk\V1\Request\CreateC2BParcelRequest;
use PuntoPost\Sdk\V1\Request\CreateC2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;
use PuntoPost\Sdk\V1\Request\GetMerchantRequest;
use PuntoPost\Sdk\V1\Request\GetParcelRequest;
use PuntoPost\Sdk\V1\Request\GetPudoRequest;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;
use PuntoPost\Sdk\V1\Request\MarkParcelReadyRequest;
use PuntoPost\Sdk\V1\Response\CoverageCheckResponse;
use PuntoPost\Sdk\V1\Response\CoverageListResponse;
use PuntoPost\Sdk\V1\Response\MerchantDetailResponse;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

use PuntoPost\Sdk\V1\Response\Model\Enum\UserType;
use PuntoPost\Sdk\V1\Response\Model\Merchant;
use PuntoPost\Sdk\V1\Response\Model\MerchantPickUpDropOff;
use PuntoPost\Sdk\V1\Response\Model\Parcel;
use PuntoPost\Sdk\V1\Response\Model\ParcelContent;
use PuntoPost\Sdk\V1\Response\Model\Person;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;
use PuntoPost\Sdk\V1\Response\Model\StatusHistoryEntry;
use PuntoPost\Sdk\V1\Response\Model\User;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;
use PuntoPost\Sdk\V1\Response\PudoDetailResponse;
use PuntoPost\Sdk\V1\Response\PudoListResponse;
use PuntoPost\Sdk\V1\Response\SuccessResponse;

class MerchantApiTest extends TestCase
{
    private MockHttpClient $httpClient;
    private MerchantApi $sut;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->sut = new MerchantApi($this->httpClient, 'https://api.example.com');
        $this->sut->setToken('test-jwt-token');
    }

    public function testGetParcelSuccess(): void
    {
        $data = [
            'detail' => [
                'id' => 'PARCEL_001',
                'tracking' => 'MXT0000000001',
                'qr_tracking' => 'https://example.com/qr/MXT0000000001.png',
                'label' => null,
                'qr_label' => null,
                'content' => ['description' => 'Libro', 'weight_kg' => 1.5],
                'status' => 'created',
                'status_history' => [['status' => 'created', 'when' => '2024-01-01T10:00:00+00:00']],
                'sender' => ['first_name' => 'Juan', 'last_name' => 'Garcia', 'email' => 'juan@example.com', 'phone' => null, 'postal_code' => null],
                'receiver' => ['first_name' => 'Ana', 'last_name' => 'Lopez', 'email' => 'ana@example.com', 'phone' => null, 'postal_code' => null],
                'origin' => null,
                'destination' => [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
                'created_at' => '2024-01-01T10:00:00+00:00',
                'expire_at' => null,
            ],
        ];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/parcels/MXT0000000001',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new ParcelDetailResponse(new Parcel(
            'PARCEL_001',
            'MXT0000000001',
            'https://example.com/qr/MXT0000000001.png',
            null,
            null,
            new ParcelContent('Libro', 1.5),
            ParcelStatus::from('created'),
            [new StatusHistoryEntry(ParcelStatus::from('created'), Date::from('2024-01-01T10:00:00+00:00'))],
            new Person('Juan', 'Garcia', 'juan@example.com', null, null),
            new Person('Ana', 'Lopez', 'ana@example.com', null, null),
            null,
            new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, Date::from('2023-01-01T00:00:00+00:00')),
            Date::from('2024-01-01T10:00:00+00:00'),
            null
        ));

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->getParcel(new GetParcelRequest('MXT0000000001')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testCancelParcelSuccess(): void
    {
        $response = new HttpResponse(204, '', []);
        $expectedRequest = [
            'method' => 'DELETE',
            'url' => 'https://api.example.com/api/merchant/v1/parcels/MXT0000000001',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new SuccessResponse(204);

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->cancelParcel(new CancelParcelRequest('MXT0000000001')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testMarkParcelReadySuccess(): void
    {
        $response = new HttpResponse(204, '', []);
        $expectedRequest = [
            'method' => 'PUT',
            'url' => 'https://api.example.com/api/merchant/v1/parcels/MXT0000000001/ready',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new SuccessResponse(204);

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->markParcelReady(new MarkParcelReadyRequest('MXT0000000001')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testCreateC2CParcelSuccess(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT_001',
            new ParcelContentData('Libro', DeclaredValue::mxn(250.0), null, 1.5),
            new PersonData('Juan', 'Garcia', 'juan@example.com', '+525512345678', '06600'),
            new PersonData('Ana', 'Lopez', 'ana@example.com', '+525598765432', '44100'),
            'PUDO_001'
        );
        $data = [
            'detail' => [
                'id' => 'PARCEL_001',
                'tracking' => 'MXT0000000001',
                'qr_tracking' => 'https://example.com/qr/MXT0000000001.png',
                'label' => null,
                'qr_label' => null,
                'content' => ['description' => 'Libro', 'weight_kg' => 1.5],
                'status' => 'created',
                'status_history' => [['status' => 'created', 'when' => '2024-01-01T10:00:00+00:00']],
                'sender' => ['first_name' => 'Juan', 'last_name' => 'Garcia', 'email' => 'juan@example.com', 'phone' => null, 'postal_code' => null],
                'receiver' => ['first_name' => 'Ana', 'last_name' => 'Lopez', 'email' => 'ana@example.com', 'phone' => null, 'postal_code' => null],
                'origin' => null,
                'destination' => [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
                'created_at' => '2024-01-01T10:00:00+00:00',
                'expire_at' => null,
            ],
        ];
        $response = new HttpResponse(
            201,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'POST',
            'url' => 'https://api.example.com/api/merchant/v1/MERCHANT_001/parcels',
            'body' => '{"content":{"description":"Libro","value":250,"currency":"MXN","weight_kg":1.5},"sender":{"first_name":"Juan","last_name":"Garcia","email":"juan@example.com","phone":"+525512345678","postal_code":"06600"},"receiver":{"first_name":"Ana","last_name":"Lopez","email":"ana@example.com","phone":"+525598765432","postal_code":"44100"},"destination_id":"PUDO_001"}',
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Content-Type' => 'application/json', 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new ParcelDetailResponse(new Parcel(
            'PARCEL_001',
            'MXT0000000001',
            'https://example.com/qr/MXT0000000001.png',
            null,
            null,
            new ParcelContent('Libro', 1.5),
            ParcelStatus::from('created'),
            [new StatusHistoryEntry(ParcelStatus::from('created'), Date::from('2024-01-01T10:00:00+00:00'))],
            new Person('Juan', 'Garcia', 'juan@example.com', null, null),
            new Person('Ana', 'Lopez', 'ana@example.com', null, null),
            null,
            new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, Date::from('2023-01-01T00:00:00+00:00')),
            Date::from('2024-01-01T10:00:00+00:00'),
            null
        ));

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->createC2CParcel($request));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testCreateB2CParcelSuccess(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT_001',
            new ParcelContentData('Celular', DeclaredValue::mxn(3500.0)),
            new PersonData('Ana', 'Lopez', 'ana@example.com', '+525598765432'),
            'PUDO_ORIGIN_001',
            'PUDO_001'
        );
        $data = [
            'detail' => [
                'id' => 'PARCEL_001',
                'tracking' => 'MXT0000000001',
                'qr_tracking' => 'https://example.com/qr/MXT0000000001.png',
                'label' => null,
                'qr_label' => null,
                'content' => ['description' => 'Celular', 'weight_kg' => null],
                'status' => 'created',
                'status_history' => [['status' => 'created', 'when' => '2024-01-01T10:00:00+00:00']],
                'sender' => ['first_name' => 'Merchant', 'last_name' => 'Bot', 'email' => 'bot@merchant.com', 'phone' => null, 'postal_code' => null],
                'receiver' => ['first_name' => 'Ana', 'last_name' => 'Lopez', 'email' => 'ana@example.com', 'phone' => null, 'postal_code' => null],
                'origin' => [
                    'id' => 'PUDO_ORIGIN_001', 'external_id' => 'MX002', 'type' => 'merchant',
                    'name' => 'Sucursal Origen', 'description' => 'Origen',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 2 #456'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
                'destination' => [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '44100', 'city' => 'Guadalajara', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
                'created_at' => '2024-01-01T10:00:00+00:00',
                'expire_at' => null,
            ],
        ];
        $response = new HttpResponse(
            201,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'POST',
            'url' => 'https://api.example.com/api/merchant/v1/MERCHANT_001/parcels/b2c',
            'body' => '{"content":{"description":"Celular","value":3500,"currency":"MXN"},"receiver":{"first_name":"Ana","last_name":"Lopez","email":"ana@example.com","phone":"+525598765432"},"origin_id":"PUDO_ORIGIN_001","destination_id":"PUDO_001"}',
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Content-Type' => 'application/json', 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new ParcelDetailResponse(new Parcel(
            'PARCEL_001',
            'MXT0000000001',
            'https://example.com/qr/MXT0000000001.png',
            null,
            null,
            new ParcelContent('Celular', null),
            ParcelStatus::from('created'),
            [new StatusHistoryEntry(ParcelStatus::from('created'), Date::from('2024-01-01T10:00:00+00:00'))],
            new Person('Merchant', 'Bot', 'bot@merchant.com', null, null),
            new Person('Ana', 'Lopez', 'ana@example.com', null, null),
            new PickUpDropOff('PUDO_ORIGIN_001', 'MX002', 'Sucursal Origen', 'Origen', new Address('06600', 'CDMX', 'Calle 2 #456', null), 'Lun-Vie: 09:00-18:00', true, Date::from('2023-01-01T00:00:00+00:00')),
            new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('44100', 'Guadalajara', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, Date::from('2023-01-01T00:00:00+00:00')),
            Date::from('2024-01-01T10:00:00+00:00'),
            null
        ));

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->createB2CParcel($request));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testCreateC2BParcelSuccess(): void
    {
        $request = new CreateC2BParcelRequest(
            'MERCHANT_001',
            new ParcelContentData('Devolucion'),
            new PersonData('Juan', 'Garcia', 'juan@example.com'),
            'PUDO_001'
        );
        $data = [
            'detail' => [
                'id' => 'PARCEL_001',
                'tracking' => 'MXT0000000001',
                'qr_tracking' => 'https://example.com/qr/MXT0000000001.png',
                'label' => null,
                'qr_label' => null,
                'content' => ['description' => 'Devolucion', 'weight_kg' => null],
                'status' => 'created',
                'status_history' => [['status' => 'created', 'when' => '2024-01-01T10:00:00+00:00']],
                'sender' => ['first_name' => 'Juan', 'last_name' => 'Garcia', 'email' => 'juan@example.com', 'phone' => null, 'postal_code' => null],
                'receiver' => ['first_name' => 'Merchant', 'last_name' => 'Bot', 'email' => 'bot@merchant.com', 'phone' => null, 'postal_code' => null],
                'origin' => null,
                'destination' => [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
                'created_at' => '2024-01-01T10:00:00+00:00',
                'expire_at' => null,
            ],
        ];
        $response = new HttpResponse(
            201,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'POST',
            'url' => 'https://api.example.com/api/merchant/v1/MERCHANT_001/parcels/c2b',
            'body' => '{"content":{"description":"Devolucion"},"sender":{"first_name":"Juan","last_name":"Garcia","email":"juan@example.com"},"destination_id":"PUDO_001"}',
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Content-Type' => 'application/json', 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new ParcelDetailResponse(new Parcel(
            'PARCEL_001',
            'MXT0000000001',
            'https://example.com/qr/MXT0000000001.png',
            null,
            null,
            new ParcelContent('Devolucion', null),
            ParcelStatus::from('created'),
            [new StatusHistoryEntry(ParcelStatus::from('created'), Date::from('2024-01-01T10:00:00+00:00'))],
            new Person('Juan', 'Garcia', 'juan@example.com', null, null),
            new Person('Merchant', 'Bot', 'bot@merchant.com', null, null),
            null,
            new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, Date::from('2023-01-01T00:00:00+00:00')),
            Date::from('2024-01-01T10:00:00+00:00'),
            null
        ));

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->createC2BParcel($request));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testListPudosWithoutRequestSuccess(): void
    {
        $data = [
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [
                [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
            ],
            'next' => null,
        ];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/pudos',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new PudoListResponse(
            new Coordinate(19.4326, -99.1332),
            [
                new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, new DateTimeImmutable('2023-01-01T00:00:00+00:00')),
            ],
            null
        );

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->listPudos());
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testListPudosWithRequestSuccess(): void
    {
        $request = new ListPudosRequest(null, '06600', 5);
        $data = [
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [
                [
                    'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                    'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                    'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                    'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
                ],
            ],
            'next' => 'https://api.example.com/api/merchant/v1/pudos?postal_code=06600&radius_km=5&cursor=5-0',
        ];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/pudos?postal_code=06600&radius_km=5',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new PudoListResponse(
            new Coordinate(19.4326, -99.1332),
            [
                new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, new DateTimeImmutable('2023-01-01T00:00:00+00:00')),
            ],
            new ListPudosRequest(null, '06600', 5, Pagination::from('5-0'))
        );

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->listPudos($request));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testGetPudoSuccess(): void
    {
        $data = [
            'detail' => [
                'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
                'name' => 'PUDO Central', 'description' => 'Punto de entrega central',
                'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1 #123'],
                'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00',
            ],
        ];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/pudos/PUDO_001',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new PudoDetailResponse(
            new PickUpDropOff('PUDO_001', 'MX001', 'PUDO Central', 'Punto de entrega central', new Address('06600', 'CDMX', 'Calle 1 #123', null), 'Lun-Vie: 09:00-18:00', true, new DateTimeImmutable('2023-01-01T00:00:00+00:00'))
        );

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->getPudo(new GetPudoRequest('PUDO_001')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testGetMerchantSuccess(): void
    {
        $data = [
            'detail' => [
                'id' => 'MERCHANT_001',
                'name' => 'Mi Tienda',
                'enabled' => true,
                'webhook_enabled' => false,
                'webhook_url' => null,
                'created_at' => '2023-01-01T00:00:00+00:00',
                'users' => [
                    ['id' => 'USER_001', 'username' => 'admin', 'email' => 'admin@tienda.com', 'type' => 'merchant', 'enabled' => true, 'created_at' => '2023-01-01T00:00:00+00:00'],
                ],
                'pudos' => [
                    ['id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'merchant', 'name' => 'Sucursal 1', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1'], 'phone' => '+525512345678', 'schedule' => 'Lun-Vie: 09-18'],
                ],
            ],
        ];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/merchants/MERCHANT_001',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new MerchantDetailResponse(
            new Merchant(
                'MERCHANT_001',
                'Mi Tienda',
                true,
                false,
                null,
                '2023-01-01T00:00:00+00:00',
                [
                    new User('USER_001', 'admin', 'admin@tienda.com', UserType::from('merchant'), true, new DateTimeImmutable('2023-01-01T00:00:00+00:00')),
                ],
                [
                    new MerchantPickUpDropOff('PUDO_001', 'MX001', 'merchant', 'Sucursal 1', new Address('06600', 'CDMX', 'Calle 1', null), '+525512345678', 'Lun-Vie: 09-18'),
                ]
            )
        );

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->getMerchant(new GetMerchantRequest('MERCHANT_001')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testCheckCoverageSuccess(): void
    {
        $data = ['covered' => true];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/coverage/06600',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new CoverageCheckResponse(true);

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->checkCoverage(new CheckCoverageRequest('06600')));
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testGetCoverageListSuccess(): void
    {
        $data = ['items' => ['06600', '44100', '64000']];
        $response = new HttpResponse(
            200,
            json_encode($data, JSON_THROW_ON_ERROR),
            ['Content-Type' => 'application/json']
        );
        $expectedRequest = [
            'method' => 'GET',
            'url' => 'https://api.example.com/api/merchant/v1/coverage',
            'body' => null,
            'headers' => ['Accept' => 'application/json', PuntoPostClient::SDK_HEADER_NAME => PuntoPostClient::SDK_HEADER_VALUE, 'Authorization' => 'Bearer test-jwt-token'],
        ];
        $expectedResponse = new CoverageListResponse(['06600', '44100', '64000']);

        $this->httpClient->queueResponse($response);

        $this->assertEquals($expectedResponse, $this->sut->getCoverageList());
        $this->assertEquals(1, $this->httpClient->getRequestCount());
        $this->assertEquals($expectedRequest, $this->httpClient->getLastRequest());
    }

    public function testGetParcelForbiddenWhenNotLoggedIn(): void
    {
        $rawBody = '{"type":"FORBIDDEN","title":"Access denied","detail":"You do not have permission to access this resource","instance":"authentication"}';
        $response = new HttpResponse(403, $rawBody, ['Content-Type' => 'application/json']);
        $expectedException = PuntoPostException::fromResponse(403, $rawBody);
        $request = new GetParcelRequest('MXT0000000001');

        $this->sut->setToken(null);
        $this->httpClient->queueResponse($response);
        $this->expectExceptionObject($expectedException);

        $this->sut->getParcel($request);
    }
}
