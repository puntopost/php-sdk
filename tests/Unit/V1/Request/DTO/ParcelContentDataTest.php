<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request\DTO;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;
use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;

class ParcelContentDataTest extends TestCase
{
    public function testToArrayWithDescriptionOnly(): void
    {
        $content = new ParcelContentData('Ropa');

        $this->assertSame(['description' => 'Ropa'], $content->toArray());
    }

    public function testToArrayWithDeclaredValue(): void
    {
        $content = new ParcelContentData('Electrónico', DeclaredValue::mxn(1500.0));

        $this->assertSame([
            'description' => 'Electrónico',
            'value' => 1500.0,
            'currency' => 'MXN',
        ], $content->toArray());
    }

    public function testToArrayWithImageUrl(): void
    {
        $content = new ParcelContentData('Zapatos', null, 'https://example.com/img.jpg');

        $this->assertSame([
            'description' => 'Zapatos',
            'image_url' => 'https://example.com/img.jpg',
        ], $content->toArray());
    }

    public function testToArrayWithWeightKg(): void
    {
        $content = new ParcelContentData('Libros', null, null, 2.5);

        $this->assertSame([
            'description' => 'Libros',
            'weight_kg' => 2.5,
        ], $content->toArray());
    }

    public function testToArrayWithAllFields(): void
    {
        $content = new ParcelContentData('Laptop', DeclaredValue::mxn(25000.0), 'https://example.com/laptop.jpg', 1.8);

        $this->assertSame([
            'description' => 'Laptop',
            'value' => 25000.0,
            'currency' => 'MXN',
            'image_url' => 'https://example.com/laptop.jpg',
            'weight_kg' => 1.8,
        ], $content->toArray());
    }

    public function testToArrayOmitsAllNullOptionals(): void
    {
        $content = new ParcelContentData('Paquete');

        $result = $content->toArray();

        $this->assertCount(1, $result);
        $this->assertArrayNotHasKey('value', $result);
        $this->assertArrayNotHasKey('currency', $result);
        $this->assertArrayNotHasKey('image_url', $result);
        $this->assertArrayNotHasKey('weight_kg', $result);
    }

    public function testToArrayWithZeroWeight(): void
    {
        $content = new ParcelContentData('Carta', null, null, 0.0);

        $result = $content->toArray();

        $this->assertArrayHasKey('weight_kg', $result);
        $this->assertSame(0.0, $result['weight_kg']);
    }

    public function testToArrayWithEmptyDescription(): void
    {
        $content = new ParcelContentData('');

        $this->assertSame(['description' => ''], $content->toArray());
    }
}
