<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\MerchantPickUpDropOff;

class MerchantPickUpDropOffTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $address = new Address('06600', 'CDMX', 'Calle 1 #123', null);
        $pudo = new MerchantPickUpDropOff('ID1', 'EXT1', 'pudo', 'PUDO Central', $address, '+5255', 'Lun-Vie');

        $this->assertSame('ID1', $pudo->getId());
        $this->assertSame('EXT1', $pudo->getExternalId());
        $this->assertSame('pudo', $pudo->getType());
        $this->assertSame('PUDO Central', $pudo->getName());
        $this->assertSame($address, $pudo->getAddress());
        $this->assertSame('+5255', $pudo->getPhone());
        $this->assertSame('Lun-Vie', $pudo->getSchedule());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $pudo = MerchantPickUpDropOff::fromArray([
            'id' => 'ID1',
            'external_id' => 'EXT1',
            'type' => 'pudo',
            'name' => 'PUDO Central',
            'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1'],
            'phone' => '+5255',
            'schedule' => 'Lun-Vie: 09:00-18:00',
        ]);

        $this->assertSame('ID1', $pudo->getId());
        $this->assertSame('EXT1', $pudo->getExternalId());
        $this->assertSame('pudo', $pudo->getType());
        $this->assertSame('PUDO Central', $pudo->getName());
        $this->assertSame('06600', $pudo->getAddress()->getPostalCode());
        $this->assertSame('+5255', $pudo->getPhone());
        $this->assertSame('Lun-Vie: 09:00-18:00', $pudo->getSchedule());
    }

    public function testFromArrayWithEmptyArrayDefaultsToEmptyStrings(): void
    {
        $pudo = MerchantPickUpDropOff::fromArray([]);

        $this->assertSame('', $pudo->getId());
        $this->assertSame('', $pudo->getExternalId());
        $this->assertSame('', $pudo->getType());
        $this->assertSame('', $pudo->getName());
        $this->assertSame('', $pudo->getPhone());
        $this->assertSame('', $pudo->getSchedule());
        $this->assertSame('', $pudo->getAddress()->getPostalCode());
    }

    public function testFromArrayWithWrongTypesDefaultToEmptyStrings(): void
    {
        $pudo = MerchantPickUpDropOff::fromArray([
            'id' => 123,
            'external_id' => null,
            'type' => ['pudo'],
            'name' => false,
            'phone' => 5255,
            'schedule' => 99,
        ]);

        $this->assertSame('', $pudo->getId());
        $this->assertSame('', $pudo->getExternalId());
        $this->assertSame('', $pudo->getType());
        $this->assertSame('', $pudo->getName());
        $this->assertSame('', $pudo->getPhone());
        $this->assertSame('', $pudo->getSchedule());
    }

    public function testFromArrayAddressDefaultsWhenNotArray(): void
    {
        $pudo = MerchantPickUpDropOff::fromArray(['address' => 'not_an_array']);

        $this->assertSame('', $pudo->getAddress()->getPostalCode());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $pudo = MerchantPickUpDropOff::fromArray(['unrelated' => 'data', 'random' => 42]);

        $this->assertSame('', $pudo->getId());
        $this->assertSame('', $pudo->getName());
    }
}
