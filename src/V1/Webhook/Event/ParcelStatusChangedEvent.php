<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Webhook\Event;

use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;
use PuntoPost\Sdk\V1\Response\Model\StatusHistoryEntry;

class ParcelStatusChangedEvent implements WebhookEventInterface
{
    public const EVENT_TYPE = 'parcel_status_changed';

    private string $id;
    private string $tracking;
    private ParcelStatus $status;
    /** @var StatusHistoryEntry[] */
    private array $statusHistory;

    /**
     * @param StatusHistoryEntry[] $statusHistory
     */
    public function __construct(string $id, string $tracking, ParcelStatus $status, array $statusHistory)
    {
        $this->id = $id;
        $this->tracking = $tracking;
        $this->status = $status;
        $this->statusHistory = $statusHistory;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $context = self::EVENT_TYPE;

        $statusHistoryRaw = Getter::requireArray($data, 'status_history', $context);
        $statusHistory = array_map(
            fn ($entry, $index): StatusHistoryEntry => StatusHistoryEntry::fromArray(
                Getter::requireArray($entry, null, sprintf('%s status_history[%s]', $context, (string) $index))
            ),
            $statusHistoryRaw,
            array_keys($statusHistoryRaw)
        );

        return new self(
            Getter::requireString($data, 'id', $context),
            Getter::requireString($data, 'tracking', $context),
            ParcelStatus::from(Getter::requireString($data, 'status', $context)),
            $statusHistory
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

    public function getStatus(): ParcelStatus
    {
        return $this->status;
    }

    /**
     * @return StatusHistoryEntry[]
     */
    public function getStatusHistory(): array
    {
        return $this->statusHistory;
    }
}
