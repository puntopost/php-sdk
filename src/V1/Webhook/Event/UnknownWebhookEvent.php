<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Webhook\Event;

class UnknownWebhookEvent implements WebhookEventInterface
{
    private string $eventType;
    /** @var array<string,mixed> */
    private array $detail;

    /**
     * @param array<string,mixed> $detail
     */
    public function __construct(string $eventType, array $detail)
    {
        $this->eventType = $eventType;
        $this->detail = $detail;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @return array<string,mixed>
     */
    public function getDetail(): array
    {
        return $this->detail;
    }
}
