<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Webhook;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelDestinationChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelOriginChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelStatusChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\UnknownWebhookEvent;
use PuntoPost\Sdk\V1\Webhook\WebhookHandler;

class WebhookHandlerTest extends TestCase
{
    public function testParseStatusChanged(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode([
            'event_type' => 'parcel_status_changed',
            'detail' => [
                'id' => '50000000000000000000000001',
                'tracking' => 'MXT0000000001',
                'status' => 'in_destination_point',
                'status_history' => [
                    ['status' => 'created', 'when' => '2025-01-02T08:15:00-06:00'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $event = $handler->parse($json);

        self::assertInstanceOf(ParcelStatusChangedEvent::class, $event);
        self::assertSame('50000000000000000000000001', $event->getId());
        self::assertTrue($event->getStatus()->isDestinationPoint());
    }

    public function testParseOriginChanged(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode([
            'event_type' => 'parcel_origin_changed',
            'detail' => [
                'id' => '50000000000000000000000001',
                'tracking' => 'MXT0000000001',
                'origin' => [
                    'id' => '30000000000000000000000004',
                    'external_id' => 'MX000003',
                    'type' => 'pudo',
                    'name' => "John's Shop",
                    'description' => 'Grocery',
                    'address' => [
                        'postal_code' => '32000',
                        'city' => 'Ciudad Juárez',
                        'address' => 'Calle Test, 1',
                        'coordinate' => ['latitude' => 31.7035, 'longitude' => -106.435],
                    ],
                    'schedule' => '',
                    'schedule_items' => [],
                    'phone' => '',
                    'enabled' => true,
                    'created_at' => '2025-01-13T07:37:13-06:00',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $event = $handler->parse($json);

        self::assertInstanceOf(ParcelOriginChangedEvent::class, $event);
        self::assertSame('30000000000000000000000004', $event->getOrigin()->getId());
    }

    public function testParseDestinationChanged(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode([
            'event_type' => 'parcel_destination_changed',
            'detail' => [
                'id' => '50000000000000000000000001',
                'tracking' => 'MXT0000000001',
                'destination' => [
                    'id' => '30000000000000000000000004',
                    'external_id' => 'MX000003',
                    'type' => 'pudo',
                    'name' => "John's Shop",
                    'description' => 'Grocery',
                    'address' => [
                        'postal_code' => '32000',
                        'city' => 'Ciudad Juárez',
                        'address' => 'Calle Test, 1',
                        'coordinate' => ['latitude' => 31.7035, 'longitude' => -106.435],
                    ],
                    'schedule' => '',
                    'schedule_items' => [],
                    'phone' => '',
                    'enabled' => true,
                    'created_at' => '2025-01-13T07:37:13-06:00',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $event = $handler->parse($json);

        self::assertInstanceOf(ParcelDestinationChangedEvent::class, $event);
        self::assertSame('30000000000000000000000004', $event->getDestination()->getId());
    }

    public function testUnknownEventCapturedByDefault(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode([
            'event_type' => 'some_future_event',
            'detail' => ['foo' => 'bar'],
        ], JSON_THROW_ON_ERROR);

        $event = $handler->parse($json);

        self::assertInstanceOf(UnknownWebhookEvent::class, $event);
        self::assertSame('some_future_event', $event->getEventType());
        self::assertSame(['foo' => 'bar'], $event->getDetail());
    }

    public function testUnknownEventIgnoredWhenConfigured(): void
    {
        $handler = new WebhookHandler(WebhookHandler::IGNORE_UNKNOWN);
        $json = json_encode([
            'event_type' => 'some_future_event',
            'detail' => ['foo' => 'bar'],
        ], JSON_THROW_ON_ERROR);

        $event = $handler->parse($json);

        self::assertNull($event);
    }

    public function testInvalidJsonThrowsException(): void
    {
        $handler = new WebhookHandler();

        $this->expectException(InvalidArgumentException::class);
        $handler->parse('not valid json');
    }

    public function testMissingEventTypeThrows(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode(['detail' => ['id' => '123']], JSON_THROW_ON_ERROR);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'event_type'");
        $handler->parse($json);
    }

    public function testMissingDetailThrows(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode(['event_type' => 'parcel_status_changed'], JSON_THROW_ON_ERROR);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'detail'");
        $handler->parse($json);
    }

    public function testMissingDetailThrowsForUnknownEventType(): void
    {
        $handler = new WebhookHandler();
        $json = json_encode(['event_type' => 'some_future_event'], JSON_THROW_ON_ERROR);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'detail'");
        $handler->parse($json);
    }
}
