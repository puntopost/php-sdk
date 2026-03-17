<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

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
            'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1'],
            'schedule' => 'Lun-Vie: 09:00-18:00', 'enabled' => true,
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

    public function testFromArrayWithEmptyArrayProducesDefaultPudo(): void
    {
        $response = PudoDetailResponse::fromArray([]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNotArrayProducesDefaultPudo(): void
    {
        $response = PudoDetailResponse::fromArray(['detail' => 'not_an_array']);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNullProducesDefaultPudo(): void
    {
        $response = PudoDetailResponse::fromArray(['detail' => null]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = PudoDetailResponse::fromArray(['pudo_id' => 'X', 'status' => 'active']);

        $this->assertSame('', $response->getDetail()->getId());
        $this->assertFalse($response->getDetail()->isEnabled());
    }

    public function testFromArrayDetailBuildsNestedAddress(): void
    {
        $response = PudoDetailResponse::fromArray(['detail' => $this->buildPudoPayload()]);

        $this->assertSame('06600', $response->getDetail()->getAddress()->getPostalCode());
        $this->assertSame('CDMX', $response->getDetail()->getAddress()->getCity());
    }
}
