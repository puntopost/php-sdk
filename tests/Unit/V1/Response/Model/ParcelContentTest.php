<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\DeclaredValue;
use PuntoPost\Sdk\V1\Response\Model\ParcelContent;

class ParcelContentTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $content = new ParcelContent('Laptop', 1.8, null, null);

        $this->assertSame('Laptop', $content->getDescription());
        $this->assertSame(1.8, $content->getWeightKg());
        $this->assertNull($content->getImageUrl());
        $this->assertNull($content->getDeclaredValue());
    }

    public function testConstructorWithNullWeight(): void
    {
        $content = new ParcelContent('Ropa', null, null, null);

        $this->assertNull($content->getWeightKg());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $content = ParcelContent::fromArray([
            'description' => 'Laptop',
            'weight_kg' => 1.8,
            'image_url' => 'https://picsum.photos/200/300',
            'declared_value' => ['value' => 20.15, 'currency' => 'MXN'],
        ]);

        $this->assertSame('Laptop', $content->getDescription());
        $this->assertSame(1.8, $content->getWeightKg());
        $this->assertSame('https://picsum.photos/200/300', $content->getImageUrl());
        $this->assertNotNull($content->getDeclaredValue());
        $this->assertSame(20.15, $content->getDeclaredValue()->getValue());
        $this->assertSame('MXN', $content->getDeclaredValue()->getCurrency());
    }

    public function testFromArrayWeightAsIntegerIsCastToFloat(): void
    {
        $content = ParcelContent::fromArray(['description' => 'Caja', 'weight_kg' => 2]);

        $this->assertSame(2.0, $content->getWeightKg());
    }

    public function testFromArrayWithoutWeightIsNull(): void
    {
        $content = ParcelContent::fromArray(['description' => 'Ropa']);

        $this->assertNull($content->getWeightKg());
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelContent::fromArray([]);
    }

    public function testFromArrayWithWrongTypeForDescriptionThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelContent::fromArray(['description' => 999, 'weight_kg' => 1.0]);
    }

    public function testFromArrayWithStringWeightIsNull(): void
    {
        $content = ParcelContent::fromArray(['description' => 'X', 'weight_kg' => '1.8']);

        $this->assertNull($content->getWeightKg());
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ParcelContent::fromArray(['foo' => 'bar', 'baz' => [1, 2, 3]]);
    }
}
