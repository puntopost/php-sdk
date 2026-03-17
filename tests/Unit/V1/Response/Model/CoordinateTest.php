<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;

class CoordinateTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $coordinate = new Coordinate(19.4326, -99.1332);

        $this->assertSame(19.4326, $coordinate->getLatitude());
        $this->assertSame(-99.1332, $coordinate->getLongitude());
    }

    public function testFromArrayWithValidData(): void
    {
        $coordinate = Coordinate::fromArray(['latitude' => 19.4326, 'longitude' => -99.1332]);

        $this->assertSame(19.4326, $coordinate->getLatitude());
        $this->assertSame(-99.1332, $coordinate->getLongitude());
    }

    public function testFromArrayWithIntegerValuesAreCastToFloat(): void
    {
        $coordinate = Coordinate::fromArray(['latitude' => 19, 'longitude' => -99]);

        $this->assertSame(19.0, $coordinate->getLatitude());
        $this->assertSame(-99.0, $coordinate->getLongitude());
    }

    public function testFromArrayWithEmptyArrayDefaultsToZero(): void
    {
        $coordinate = Coordinate::fromArray([]);

        $this->assertSame(0.0, $coordinate->getLatitude());
        $this->assertSame(0.0, $coordinate->getLongitude());
    }

    public function testFromArrayWithStringValuesDefaultsToZero(): void
    {
        $coordinate = Coordinate::fromArray(['latitude' => '19.4326', 'longitude' => '-99.1332']);

        $this->assertSame(0.0, $coordinate->getLatitude());
        $this->assertSame(0.0, $coordinate->getLongitude());
    }

    public function testFromArrayWithNullValuesDefaultsToZero(): void
    {
        $coordinate = Coordinate::fromArray(['latitude' => null, 'longitude' => null]);

        $this->assertSame(0.0, $coordinate->getLatitude());
        $this->assertSame(0.0, $coordinate->getLongitude());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $coordinate = Coordinate::fromArray(['foo' => 'bar', 'baz' => 42]);

        $this->assertSame(0.0, $coordinate->getLatitude());
        $this->assertSame(0.0, $coordinate->getLongitude());
    }
}
