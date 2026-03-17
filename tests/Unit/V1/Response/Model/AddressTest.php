<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;

class AddressTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $coordinate = new Coordinate(19.4326, -99.1332);
        $address = new Address('06600', 'CDMX', 'Calle 1 #123', $coordinate);

        $this->assertSame('06600', $address->getPostalCode());
        $this->assertSame('CDMX', $address->getCity());
        $this->assertSame('Calle 1 #123', $address->getAddress());
        $this->assertSame($coordinate, $address->getCoordinate());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $address = Address::fromArray([
            'postal_code' => '06600',
            'city' => 'CDMX',
            'address' => 'Calle 1 #123',
            'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332],
        ]);

        $this->assertSame('06600', $address->getPostalCode());
        $this->assertSame('CDMX', $address->getCity());
        $this->assertSame('Calle 1 #123', $address->getAddress());
        $this->assertSame(19.4326, $address->getCoordinate()->getLatitude());
    }

    public function testFromArrayMissingCoordinateThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'coordinate'");

        Address::fromArray([
            'postal_code' => '06600',
            'city' => 'CDMX',
            'address' => 'Calle 1 #123',
        ]);
    }

    public function testFromArrayCoordinateNotArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'coordinate'");

        Address::fromArray([
            'postal_code' => '06600',
            'city' => 'CDMX',
            'address' => 'Calle 1',
            'coordinate' => 'not_an_array',
        ]);
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::fromArray([]);
    }

    public function testFromArrayWithWrongTypesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::fromArray([
            'postal_code' => 6600,
            'city' => ['CDMX'],
            'address' => true,
            'coordinate' => ['latitude' => 1.0, 'longitude' => 2.0],
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Address::fromArray(['unexpected' => 99, 'random' => null]);
    }
}
