<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request\DTO;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\Coordinate;

class CoordinateTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $coordinate = new Coordinate(19.4326, -99.1332);

        $this->assertSame(19.4326, $coordinate->getLatitude());
        $this->assertSame(-99.1332, $coordinate->getLongitude());
    }

    public function testWithNegativeLatitudeAndLongitude(): void
    {
        $coordinate = new Coordinate(-33.8688, -70.6693);

        $this->assertSame(-33.8688, $coordinate->getLatitude());
        $this->assertSame(-70.6693, $coordinate->getLongitude());
    }

    public function testWithZeroValues(): void
    {
        $coordinate = new Coordinate(0.0, 0.0);

        $this->assertSame(0.0, $coordinate->getLatitude());
        $this->assertSame(0.0, $coordinate->getLongitude());
    }

    public function testWithHighPrecisionValues(): void
    {
        $coordinate = new Coordinate(19.432607530148446, -99.13319396972656);

        $this->assertSame(19.432607530148446, $coordinate->getLatitude());
        $this->assertSame(-99.13319396972656, $coordinate->getLongitude());
    }

    public function testWithMaxValidValues(): void
    {
        $coordinate = new Coordinate(90.0, 180.0);

        $this->assertSame(90.0, $coordinate->getLatitude());
        $this->assertSame(180.0, $coordinate->getLongitude());
    }

    public function testWithMinValidValues(): void
    {
        $coordinate = new Coordinate(-90.0, -180.0);

        $this->assertSame(-90.0, $coordinate->getLatitude());
        $this->assertSame(-180.0, $coordinate->getLongitude());
    }
}
