<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\PudoListResponse;

class PudoListResponseTest extends TestCase
{
    /**
     * @return array<string,mixed>
     */
    private function buildPudoData(): array
    {
        return [
            'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
            'name' => 'PUDO Central', 'description' => 'Punto de entrega',
            'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1'],
            'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true,
            'created_at' => '2024-01-15T00:00:00+00:00',
        ];
    }

    public function testFromArrayWithFullPayloadAndNextUrl(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [$this->buildPudoData()],
            'next' => 'https://api.example.com/pudos?postal_code=06600&radius_km=5&cursor=5-10',
        ]);

        $this->assertSame(19.4326, $response->getCoordinate()->getLatitude());
        $this->assertSame(-99.1332, $response->getCoordinate()->getLongitude());
        $this->assertCount(1, $response->getItems());
        $this->assertSame('PUDO_001', $response->getItems()[0]->getId());
        $this->assertNotNull($response->getNext());
    }

    public function testFromArrayNextIsNullWhenAbsent(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [],
            'next' => null,
        ]);

        $this->assertNull($response->getNext());
    }

    public function testFromArrayNextParsesUrlIntoListPudosRequest(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [],
            'next' => 'https://api.example.com/pudos?postal_code=06600&radius_km=5&cursor=5-10',
        ]);

        $next = $response->getNext();
        $this->assertInstanceOf(ListPudosRequest::class, $next);

        $params = $next->toQueryParams();
        $this->assertSame('06600', $params['postal_code']);
        $this->assertSame(5, $params['radius_km']);
        $this->assertSame('5-10', $params['cursor']);
    }

    public function testFromArrayNextPreservesRoundTripCursor(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => [],
            'next' => 'https://api.example.com/pudos?cursor=5-10',
        ]);

        $this->assertNotNull($response->getNext());
        $this->assertSame('5-10', $response->getNext()->toQueryParams()['cursor']);
    }

    public function testFromArrayWithEmptyArrayProducesDefaults(): void
    {
        $response = PudoListResponse::fromArray([]);

        $this->assertSame(0.0, $response->getCoordinate()->getLatitude());
        $this->assertSame(0.0, $response->getCoordinate()->getLongitude());
        $this->assertCount(0, $response->getItems());
        $this->assertNull($response->getNext());
    }

    public function testFromArrayIgnoresNonArrayItems(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
            'items' => ['not_array', 99, null, $this->buildPudoData()],
            'next' => null,
        ]);

        $this->assertCount(1, $response->getItems());
    }

    public function testFromArrayCoordinateDefaultsWhenNotArray(): void
    {
        $response = PudoListResponse::fromArray(['coordinate' => 'not_array', 'items' => [], 'next' => null]);

        $this->assertSame(0.0, $response->getCoordinate()->getLatitude());
    }

    public function testFromArrayNextIsNullWhenNotString(): void
    {
        $response = PudoListResponse::fromArray([
            'coordinate' => [],
            'items' => [],
            'next' => ['some_url'],
        ]);

        $this->assertNull($response->getNext());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = PudoListResponse::fromArray(['foo' => 'bar', 'baz' => 42]);

        $this->assertSame(0.0, $response->getCoordinate()->getLatitude());
        $this->assertCount(0, $response->getItems());
        $this->assertNull($response->getNext());
    }
}
