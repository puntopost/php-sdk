<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Webhook\Event;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelDestinationChangedEvent;

class ParcelDestinationChangedEventTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => '50000000000000000000000001',
            'tracking' => 'MXT0000000001',
            'destination' => [
                'id' => '30000000000000000000000004',
                'external_id' => 'MX000003',
                'name' => "John's Shop",
                'description' => 'Grocery',
                'address' => [
                    'postal_code' => '32000',
                    'city' => 'Ciudad Juárez',
                    'address' => 'Chihuahua, Ciudad Juárez, Progresista, Calle Tío Pepe, 2',
                    'coordinate' => ['latitude' => 31.7035, 'longitude' => -106.4350],
                ],
                'schedule' => 'Lun, Mie, Vie: 09:30 - 17:00.',
                'enabled' => true,
                'created_at' => '2025-01-13T07:37:13-06:00',
            ],
        ];

        $event = ParcelDestinationChangedEvent::fromArray($data);

        self::assertSame('parcel_destination_changed', $event->getEventType());
        self::assertSame('50000000000000000000000001', $event->getId());
        self::assertSame('MXT0000000001', $event->getTracking());
        self::assertSame('30000000000000000000000004', $event->getDestination()->getId());
        self::assertSame('MX000003', $event->getDestination()->getExternalId());
        self::assertSame("John's Shop", $event->getDestination()->getName());
        self::assertSame('32000', $event->getDestination()->getAddress()->getPostalCode());
    }

    public function testFromArrayThrowsOnMissingId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'id'");

        ParcelDestinationChangedEvent::fromArray([
            'tracking' => 'MXT0000000001',
            'destination' => [],
        ]);
    }

    public function testFromArrayThrowsOnMissingTracking(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'tracking'");

        ParcelDestinationChangedEvent::fromArray([
            'id' => '50000000000000000000000001',
            'destination' => [],
        ]);
    }

    public function testFromArrayThrowsOnMissingDestination(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'destination'");

        ParcelDestinationChangedEvent::fromArray([
            'id' => '50000000000000000000000001',
            'tracking' => 'MXT0000000001',
        ]);
    }
}
