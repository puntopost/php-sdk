<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Webhook\Event;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Webhook\Event\UnknownWebhookEvent;

class UnknownWebhookEventTest extends TestCase
{
    public function testGetters(): void
    {
        $detail = ['id' => 'abc', 'foo' => 'bar'];
        $event = new UnknownWebhookEvent('some_new_event', $detail);

        self::assertSame('some_new_event', $event->getEventType());
        self::assertSame($detail, $event->getDetail());
    }

    public function testEmptyDetail(): void
    {
        $event = new UnknownWebhookEvent('another_event', []);

        self::assertSame('another_event', $event->getEventType());
        self::assertSame([], $event->getDetail());
    }
}
