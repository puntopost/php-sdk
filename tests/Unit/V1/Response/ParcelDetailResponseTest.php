<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

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
                'description' => '', 'address' => [], 'schedule' => '', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
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

    public function testFromArrayWithEmptyArrayProducesDefaultParcel(): void
    {
        $response = ParcelDetailResponse::fromArray([]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNotArrayProducesDefaultParcel(): void
    {
        $response = ParcelDetailResponse::fromArray(['detail' => 'not_an_array']);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNullProducesDefaultParcel(): void
    {
        $response = ParcelDetailResponse::fromArray(['detail' => null]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = ParcelDetailResponse::fromArray(['parcel_id' => 'X', 'message' => 'ok']);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayDetailBuildsNestedModels(): void
    {
        $response = ParcelDetailResponse::fromArray(['detail' => $this->buildParcelPayload()]);

        $this->assertSame('Juan', $response->getDetail()->getSender()->getFirstName());
        $this->assertSame('Ana', $response->getDetail()->getReceiver()->getFirstName());
        $this->assertSame('Ropa', $response->getDetail()->getContent()->getDescription());
    }
}
