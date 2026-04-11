<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;

class ParcelDetailResponseTest extends TestCase
{
    /**
     * @return array<string,mixed>
     */
    private function buildParcelPayload(): array
    {
        return [
            'id' => 'PCL-001', 'tracking' => 'TRK-001', 'qr_tracking' => 'QR-001',
            'content' => ['description' => 'Ropa'],
            'status' => 'created',
            'status_history' => [],
            'sender' => ['first_name' => 'Juan', 'last_name' => 'G', 'email' => 'j@e.com'],
            'receiver' => ['first_name' => 'Ana', 'last_name' => 'L', 'email' => 'a@e.com'],
            'destination' => ['id' => 'D1', 'external_id' => 'E1', 'type' => 'pudo', 'name' => 'N',
                'description' => '', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]], 'schedule' => '', 'schedule_items' => [], 'phone' => '', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
            'created_at' => '2024-01-15T10:00:00+00:00',
        ];
    }

    public function testFromArrayWithValidPayload(): void
    {
        $response = ParcelDetailResponse::fromArray(['detail' => $this->buildParcelPayload()]);

        $this->assertSame('PCL-001', $response->getDetail()->getId());
        $this->assertSame('TRK-001', $response->getDetail()->getTracking());
        $this->assertSame('created', $response->getDetail()->getStatus()->getValue());
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelDetailResponse::fromArray([]);
    }

    public function testFromArrayWithDetailNotArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelDetailResponse::fromArray(['detail' => 'not_an_array']);
    }

    public function testFromArrayWithDetailNullThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelDetailResponse::fromArray(['detail' => null]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelDetailResponse::fromArray(['parcel_id' => 'X', 'message' => 'ok']);
    }

    public function testFromArrayDetailBuildsNestedModels(): void
    {
        $response = ParcelDetailResponse::fromArray(['detail' => $this->buildParcelPayload()]);

        $this->assertSame('Juan', $response->getDetail()->getSender()->getFirstName());
        $this->assertSame('Ana', $response->getDetail()->getReceiver()->getFirstName());
        $this->assertSame('Ropa', $response->getDetail()->getContent()->getDescription());
    }
}
