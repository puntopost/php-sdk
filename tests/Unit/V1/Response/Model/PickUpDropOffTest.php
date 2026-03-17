<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;

class PickUpDropOffTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $address = new Address('06600', 'CDMX', 'Calle 1', new Coordinate(19.4326, -99.1332));
        $createdAt = new DateTimeImmutable('2024-01-15T00:00:00+00:00');
        $pudo = new PickUpDropOff('ID1', 'EXT1', 'PUDO Central', 'Desc', $address, 'Lun-Vie', true, $createdAt);

        $this->assertSame('ID1', $pudo->getId());
        $this->assertSame('EXT1', $pudo->getExternalId());
        $this->assertSame('PUDO Central', $pudo->getName());
        $this->assertSame('Desc', $pudo->getDescription());
        $this->assertSame($address, $pudo->getAddress());
        $this->assertSame('Lun-Vie', $pudo->getSchedule());
        $this->assertTrue($pudo->isEnabled());
        $this->assertSame($createdAt, $pudo->getCreatedAt());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $pudo = PickUpDropOff::fromArray([
            'id' => 'PUDO_001',
            'external_id' => 'MX001',
            'type' => 'pudo',
            'name' => 'PUDO Central',
            'description' => 'Punto de entrega',
            'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]],
            'schedule' => 'Lun-Vie: 09:00-18:00',
            'enabled' => true,
            'created_at' => '2024-01-15T00:00:00+00:00',
        ]);

        $this->assertSame('PUDO_001', $pudo->getId());
        $this->assertSame('MX001', $pudo->getExternalId());
        $this->assertSame('PUDO Central', $pudo->getName());
        $this->assertSame('Punto de entrega', $pudo->getDescription());
        $this->assertSame('06600', $pudo->getAddress()->getPostalCode());
        $this->assertSame('Lun-Vie: 09:00-18:00', $pudo->getSchedule());
        $this->assertTrue($pudo->isEnabled());
        $this->assertSame('2024-01-15', $pudo->getCreatedAt()->format('Y-m-d'));
    }

    public function testFromArrayEnabledIsFalseWhenAbsentThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PickUpDropOff::fromArray(['id' => 'X', 'external_id' => 'Y', 'name' => 'Z',
            'description' => '', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]], 'schedule' => '', 'created_at' => '2024-01-01T00:00:00+00:00']);
    }

    public function testFromArrayEnabledIsFalseWhenNotBoolThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PickUpDropOff::fromArray(['id' => 'X', 'external_id' => 'Y', 'name' => 'Z',
            'description' => '', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]], 'schedule' => '', 'enabled' => 1, 'created_at' => '2024-01-01T00:00:00+00:00']);
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PickUpDropOff::fromArray([]);
    }

    public function testFromArrayWithWrongTypesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PickUpDropOff::fromArray([
            'id' => 99,
            'external_id' => null,
            'name' => true,
            'description' => 42,
            'schedule' => false,
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PickUpDropOff::fromArray(['unrelated' => 'stuff', 'random_number' => 42]);
    }
}
