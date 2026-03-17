<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Webhook\Event;

interface WebhookEventInterface
{
    public function getEventType(): string;
}
