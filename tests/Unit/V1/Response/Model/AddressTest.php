<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

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

    public function testConstructorWithNullCoordinate(): void
    {
        $address = new Address('06600', 'CDMX', 'Calle 1 #123', null);

        $this->assertNull($address->getCoordinate());
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
        $this->assertNotNull($address->getCoordinate());
        $this->assertSame(19.4326, $address->getCoordinate()->getLatitude());
    }

    public function testFromArrayWithoutCoordinate(): void
    {
        $address = Address::fromArray([
            'postal_code' => '06600',
            'city' => 'CDMX',
            'address' => 'Calle 1 #123',
        ]);

        $this->assertNull($address->getCoordinate());
    }

    public function testFromArrayWithEmptyArrayDefaultsToEmptyStrings(): void
    {
        $address = Address::fromArray([]);

        $this->assertSame('', $address->getPostalCode());
        $this->assertSame('', $address->getCity());
        $this->assertSame('', $address->getAddress());
        $this->assertNull($address->getCoordinate());
    }

    public function testFromArrayWithWrongTypesDefaultsToEmptyStrings(): void
    {
        $address = Address::fromArray([
            'postal_code' => 6600,
            'city' => ['CDMX'],
            'address' => true,
        ]);

        $this->assertSame('', $address->getPostalCode());
        $this->assertSame('', $address->getCity());
        $this->assertSame('', $address->getAddress());
    }

    public function testFromArrayCoordinateIgnoredWhenNotArray(): void
    {
        $address = Address::fromArray([
            'postal_code' => '06600',
            'city' => 'CDMX',
            'address' => 'Calle 1',
            'coordinate' => 'not_an_array',
        ]);

        $this->assertNull($address->getCoordinate());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $address = Address::fromArray(['unexpected' => 99, 'random' => null]);

        $this->assertSame('', $address->getPostalCode());
        $this->assertSame('', $address->getCity());
        $this->assertSame('', $address->getAddress());
        $this->assertNull($address->getCoordinate());
    }
}
