<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\ParcelContent;

class ParcelContentTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $content = new ParcelContent('Laptop', 1.8);

        $this->assertSame('Laptop', $content->getDescription());
        $this->assertSame(1.8, $content->getWeightKg());
    }

    public function testConstructorWithNullWeight(): void
    {
        $content = new ParcelContent('Ropa', null);

        $this->assertNull($content->getWeightKg());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $content = ParcelContent::fromArray(['description' => 'Laptop', 'weight_kg' => 1.8]);

        $this->assertSame('Laptop', $content->getDescription());
        $this->assertSame(1.8, $content->getWeightKg());
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
