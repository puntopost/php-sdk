<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Webhook\Event;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelStatusChangedEvent;

class ParcelStatusChangedEventTest extends TestCase
{
    public function testFromArrayWithFullData(): void
    {
        $data = [
            'id' => '50000000000000000000000001',
            'tracking' => 'MXT0000000001',
            'status' => 'in_destination_point',
            'status_history' => [
                ['status' => 'created', 'when' => '2025-01-02T08:15:00-06:00'],
                ['status' => 'in_origin_point', 'when' => '2025-01-03T12:30:00-06:00'],
                ['status' => 'in_destination_point', 'when' => '2025-01-04T17:45:00-06:00'],
            ],
        ];

        $event = ParcelStatusChangedEvent::fromArray($data);

        self::assertSame('parcel_status_changed', $event->getEventType());
        self::assertSame('50000000000000000000000001', $event->getId());
        self::assertSame('MXT0000000001', $event->getTracking());
        self::assertTrue($event->getStatus()->isDestinationPoint());
        self::assertCount(3, $event->getStatusHistory());
        self::assertTrue($event->getStatusHistory()[0]->getStatus()->isCreated());
        self::assertSame('2025-01-02', $event->getStatusHistory()[0]->getWhen()->format('Y-m-d'));
    }

    public function testFromArrayThrowsOnMissingId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'id'");

        ParcelStatusChangedEvent::fromArray([
            'tracking' => 'MXT0000000001',
            'status' => 'created',
            'status_history' => [],
        ]);
    }

    public function testFromArrayThrowsOnMissingTracking(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'tracking'");

        ParcelStatusChangedEvent::fromArray([
            'id' => '50000000000000000000000001',
            'status' => 'created',
            'status_history' => [],
        ]);
    }

    public function testFromArrayThrowsOnMissingStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'status'");

        ParcelStatusChangedEvent::fromArray([
            'id' => '50000000000000000000000001',
            'tracking' => 'MXT0000000001',
            'status_history' => [],
        ]);
    }

    public function testFromArrayThrowsOnMissingStatusHistory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'status_history'");

        ParcelStatusChangedEvent::fromArray([
            'id' => '50000000000000000000000001',
            'tracking' => 'MXT0000000001',
            'status' => 'created',
        ]);
    }
}
