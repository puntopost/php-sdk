<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Webhook\Event;

use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;

class ParcelOriginChangedEvent implements WebhookEventInterface
{
    public const EVENT_TYPE = 'parcel_origin_changed';

    private string $id;
    private string $tracking;
    private PickUpDropOff $origin;

    public function __construct(string $id, string $tracking, PickUpDropOff $origin)
    {
        $this->id = $id;
        $this->tracking = $tracking;
        $this->origin = $origin;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $context = self::EVENT_TYPE;

        return new self(
            Getter::requireString($data, 'id', $context),
            Getter::requireString($data, 'tracking', $context),
            PickUpDropOff::fromArray(Getter::requireArray($data, 'origin', $context))
        );
    }

    public function getEventType(): string
    {
        return self::EVENT_TYPE;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTracking(): string
    {
        return $this->tracking;
    }

    public function getOrigin(): PickUpDropOff
    {
        return $this->origin;
    }
}
