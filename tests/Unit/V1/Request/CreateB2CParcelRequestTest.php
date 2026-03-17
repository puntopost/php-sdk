<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\CreateB2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class CreateB2CParcelRequestTest extends TestCase
{
    public function testGetMerchantId(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Ropa'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_ORIGIN',
            'PUDO_DEST'
        );

        $this->assertSame('MERCHANT-001', $request->getMerchantId());
    }

    public function testToArrayWithMinimalContent(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Ropa'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_ORIGIN',
            'PUDO_DEST'
        );

        $this->assertSame([
            'content' => ['description' => 'Ropa'],
            'receiver' => ['first_name' => 'Ana', 'last_name' => 'López', 'email' => 'ana@example.com'],
            'origin_id' => 'PUDO_ORIGIN',
            'destination_id' => 'PUDO_DEST',
        ], $request->toArray());
    }

    public function testToArrayWithFullContent(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Laptop', DeclaredValue::mxn(25000.0), null, 1.8),
            new PersonData('Ana', 'López', 'ana@example.com', '+525598765432', '64000'),
            'PUDO_ORIGIN_001',
            'PUDO_DEST_001'
        );

        /** @var array<string,array<string,mixed>> $result */
        $result = $request->toArray();

        $this->assertSame('Laptop', $result['content']['description']);
        $this->assertSame(25000.0, $result['content']['value']);
        $this->assertSame(1.8, $result['content']['weight_kg']);
        $this->assertSame('+525598765432', $result['receiver']['phone']);
        $this->assertSame('64000', $result['receiver']['postal_code']);
        $this->assertSame('PUDO_ORIGIN_001', $result['origin_id']);
        $this->assertSame('PUDO_DEST_001', $result['destination_id']);
    }

    public function testToArrayDoesNotIncludeMerchantId(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Paquete'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_ORIGIN',
            'PUDO_DEST'
        );

        $this->assertArrayNotHasKey('merchant_id', $request->toArray());
    }

    public function testToArrayDoesNotIncludeSender(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Paquete'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_ORIGIN',
            'PUDO_DEST'
        );

        $this->assertArrayNotHasKey('sender', $request->toArray());
    }

    public function testToArrayStructureKeys(): void
    {
        $request = new CreateB2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Paquete'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_ORIGIN',
            'PUDO_DEST'
        );

        $result = $request->toArray();

        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('receiver', $result);
        $this->assertArrayHasKey('origin_id', $result);
        $this->assertArrayHasKey('destination_id', $result);
        $this->assertCount(4, $result);
    }
}
