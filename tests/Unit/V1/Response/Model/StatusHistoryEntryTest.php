<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;
use PuntoPost\Sdk\V1\Response\Model\StatusHistoryEntry;

class StatusHistoryEntryTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $status = ParcelStatus::from(ParcelStatus::CREATED);
        $when = new DateTimeImmutable('2024-01-15T10:00:00+00:00');
        $entry = new StatusHistoryEntry($status, $when);

        $this->assertSame($status, $entry->getStatus());
        $this->assertSame($when, $entry->getWhen());
    }

    public function testFromArrayWithValidData(): void
    {
        $entry = StatusHistoryEntry::fromArray([
            'status' => 'created',
            'when' => '2024-01-15T10:00:00+00:00',
        ]);

        $this->assertSame('created', $entry->getStatus()->getValue());
        $this->assertInstanceOf(DateTimeImmutable::class, $entry->getWhen());
        $this->assertSame('2024-01-15', $entry->getWhen()->format('Y-m-d'));
    }

    public function testFromArrayWithUnknownStatusIsAccepted(): void
    {
        $entry = StatusHistoryEntry::fromArray([
            'status' => 'brand_new_future_status',
            'when' => '2024-01-15T10:00:00+00:00',
        ]);

        $this->assertSame('brand_new_future_status', $entry->getStatus()->getValue());
    }

    public function testFromArrayWithEmptyArrayProducesDefaults(): void
    {
        $entry = StatusHistoryEntry::fromArray([]);

        $this->assertSame('', $entry->getStatus()->getValue());
        $this->assertInstanceOf(DateTimeImmutable::class, $entry->getWhen());
    }

    public function testFromArrayWithWrongTypeForStatus(): void
    {
        $entry = StatusHistoryEntry::fromArray(['status' => 42, 'when' => '2024-01-15T10:00:00+00:00']);

        $this->assertSame('', $entry->getStatus()->getValue());
    }

    public function testFromArrayWithWrongTypeForWhen(): void
    {
        $entry = StatusHistoryEntry::fromArray(['status' => 'created', 'when' => ['not', 'a', 'string']]);

        $this->assertSame('created', $entry->getStatus()->getValue());
        $this->assertInstanceOf(DateTimeImmutable::class, $entry->getWhen());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $entry = StatusHistoryEntry::fromArray(['totally' => 'wrong', 'keys' => 99]);

        $this->assertSame('', $entry->getStatus()->getValue());
        $this->assertInstanceOf(DateTimeImmutable::class, $entry->getWhen());
    }
}
