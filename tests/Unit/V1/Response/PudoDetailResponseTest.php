<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\PudoDetailResponse;

class PudoDetailResponseTest extends TestCase
{
    /**
     * @return array<string,mixed>
     */
    private function buildPudoPayload(): array
    {
        return [
            'id' => 'PUDO_001', 'external_id' => 'MX001', 'type' => 'pudo',
            'name' => 'PUDO Central', 'description' => 'Punto de entrega',
            'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]],
            'schedule' => 'Lun-Vie: 09:00-18:00', 'schedule_items' => [], 'phone' => '+523334445556', 'enabled' => true,
            'created_at' => '2024-01-15T00:00:00+00:00',
        ];
    }

    public function testFromArrayWithValidPayload(): void
    {
        $response = PudoDetailResponse::fromArray(['detail' => $this->buildPudoPayload()]);

        $this->assertSame('PUDO_001', $response->getDetail()->getId());
        $this->assertSame('PUDO Central', $response->getDetail()->getName());
        $this->assertTrue($response->getDetail()->isEnabled());
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PudoDetailResponse::fromArray([]);
    }

    public function testFromArrayWithDetailNotArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PudoDetailResponse::fromArray(['detail' => 'not_an_array']);
    }

    public function testFromArrayWithDetailNullThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PudoDetailResponse::fromArray(['detail' => null]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PudoDetailResponse::fromArray(['pudo_id' => 'X', 'status' => 'active']);
    }

    public function testFromArrayDetailBuildsNestedAddress(): void
    {
        $response = PudoDetailResponse::fromArray(['detail' => $this->buildPudoPayload()]);

        $this->assertSame('06600', $response->getDetail()->getAddress()->getPostalCode());
        $this->assertSame('CDMX', $response->getDetail()->getAddress()->getCity());
    }
}
