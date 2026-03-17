<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Webhook;

use InvalidArgumentException;
use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelDestinationChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelOriginChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\ParcelStatusChangedEvent;
use PuntoPost\Sdk\V1\Webhook\Event\UnknownWebhookEvent;
use PuntoPost\Sdk\V1\Webhook\Event\WebhookEventInterface;

class WebhookHandler
{
    public const CAPTURE_UNKNOWN = 'capture';
    public const IGNORE_UNKNOWN = 'ignore';

    private const PAYLOAD_CONTEXT = 'webhook payload';

    private string $unknownEventStrategy;

    public function __construct(string $unknownEventStrategy = self::CAPTURE_UNKNOWN)
    {
        $this->unknownEventStrategy = $unknownEventStrategy;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function parse(string $json): ?WebhookEventInterface
    {
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid webhook JSON payload');
        }

        $eventType = Getter::requireString($data, 'event_type', self::PAYLOAD_CONTEXT);
        $detail = Getter::requireArray($data, 'detail', self::PAYLOAD_CONTEXT);

        switch ($eventType) {
            case ParcelStatusChangedEvent::EVENT_TYPE:
                return ParcelStatusChangedEvent::fromArray($detail);
            case ParcelOriginChangedEvent::EVENT_TYPE:
                return ParcelOriginChangedEvent::fromArray($detail);
            case ParcelDestinationChangedEvent::EVENT_TYPE:
                return ParcelDestinationChangedEvent::fromArray($detail);
            default:
                if ($this->unknownEventStrategy === self::IGNORE_UNKNOWN) {
                    return null;
                }

                return new UnknownWebhookEvent($eventType, $detail);
        }
    }
}
