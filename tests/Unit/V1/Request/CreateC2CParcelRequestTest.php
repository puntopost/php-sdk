<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\CreateC2CParcelRequest;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class CreateC2CParcelRequestTest extends TestCase
{
    public function testGetMerchantId(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Ropa'),
            new PersonData('Juan', 'García', 'juan@example.com'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_DEST'
        );

        $this->assertSame('MERCHANT-001', $request->getMerchantId());
    }

    public function testToArrayWithMinimalContent(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Ropa'),
            new PersonData('Juan', 'García', 'juan@example.com'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_DEST'
        );

        $this->assertSame([
            'content' => ['description' => 'Ropa'],
            'sender' => ['first_name' => 'Juan', 'last_name' => 'García', 'email' => 'juan@example.com'],
            'receiver' => ['first_name' => 'Ana', 'last_name' => 'López', 'email' => 'ana@example.com'],
            'destination_id' => 'PUDO_DEST',
        ], $request->toArray());
    }

    public function testToArrayWithFullContent(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Laptop', DeclaredValue::mxn(25000.0), 'https://example.com/img.jpg', 1.8),
            new PersonData('Juan', 'García', 'juan@example.com', '+525512345678', '06600'),
            new PersonData('Ana', 'López', 'ana@example.com', '+525598765432'),
            'PUDO_DEST_001'
        );

        /** @var array<string,array<string,mixed>> $result */
        $result = $request->toArray();

        $this->assertSame('Laptop', $result['content']['description']);
        $this->assertSame(25000.0, $result['content']['value']);
        $this->assertSame('MXN', $result['content']['currency']);
        $this->assertSame('https://example.com/img.jpg', $result['content']['image_url']);
        $this->assertSame(1.8, $result['content']['weight_kg']);
        $this->assertSame('+525512345678', $result['sender']['phone']);
        $this->assertSame('06600', $result['sender']['postal_code']);
        $this->assertSame('+525598765432', $result['receiver']['phone']);
        $this->assertSame('PUDO_DEST_001', $result['destination_id']);
    }

    public function testToArrayDoesNotIncludeMerchantId(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Paquete'),
            new PersonData('Juan', 'García', 'juan@example.com'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_DEST'
        );

        $this->assertArrayNotHasKey('merchant_id', $request->toArray());
    }

    public function testToArrayStructureKeys(): void
    {
        $request = new CreateC2CParcelRequest(
            'MERCHANT-001',
            new ParcelContentData('Paquete'),
            new PersonData('Juan', 'García', 'juan@example.com'),
            new PersonData('Ana', 'López', 'ana@example.com'),
            'PUDO_DEST'
        );

        $result = $request->toArray();

        $this->assertArrayHasKey('content', $result);
        $this->assertArrayHasKey('sender', $result);
        $this->assertArrayHasKey('receiver', $result);
        $this->assertArrayHasKey('destination_id', $result);
        $this->assertCount(4, $result);
    }
}
