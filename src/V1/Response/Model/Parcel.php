<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

class Parcel
{
    private string $id;
    private string $tracking;
    private string $qrTracking;
    private ?string $label;
    private ?string $qrLabel;
    private ParcelContent $content;
    private ParcelStatus $status;
    /** @var StatusHistoryEntry[] */
    private array $statusHistory;
    private Person $sender;
    private Person $receiver;
    private ?PickUpDropOff $origin;
    private PickUpDropOff $destination;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $expireAt;

    /**
     * @param StatusHistoryEntry[] $statusHistory
     */
    public function __construct(
        string $id,
        string $tracking,
        string $qrTracking,
        ?string $label,
        ?string $qrLabel,
        ParcelContent $content,
        ParcelStatus $status,
        array $statusHistory,
        Person $sender,
        Person $receiver,
        ?PickUpDropOff $origin,
        PickUpDropOff $destination,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $expireAt
    ) {
        $this->id = $id;
        $this->tracking = $tracking;
        $this->qrTracking = $qrTracking;
        $this->label = $label;
        $this->qrLabel = $qrLabel;
        $this->content = $content;
        $this->status = $status;
        $this->statusHistory = $statusHistory;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->origin = $origin;
        $this->destination = $destination;
        $this->createdAt = $createdAt;
        $this->expireAt = $expireAt;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $statusHistoryRaw = Getter::requireArray($data, 'status_history', 'Parcel');
        $statusHistory = array_map(
            fn ($entry, $index): StatusHistoryEntry => StatusHistoryEntry::fromArray(
                Getter::requireArray($entry, null, sprintf('Parcel status_history[%s]', (string) $index))
            ),
            $statusHistoryRaw,
            array_keys($statusHistoryRaw)
        );

        $originData = Getter::optionalArray($data, 'origin');
        $expireAt = Getter::optionalString($data, 'expire_at');

        return new self(
            Getter::requireString($data, 'id', 'Parcel'),
            Getter::requireString($data, 'tracking', 'Parcel'),
            Getter::requireString($data, 'qr_tracking', 'Parcel'),
            Getter::optionalString($data, 'label'),
            Getter::optionalString($data, 'qr_label'),
            ParcelContent::fromArray(Getter::requireArray($data, 'content', 'Parcel')),
            ParcelStatus::from(Getter::requireString($data, 'status', 'Parcel')),
            $statusHistory,
            Person::fromArray(Getter::requireArray($data, 'sender', 'Parcel')),
            Person::fromArray(Getter::requireArray($data, 'receiver', 'Parcel')),
            $originData !== null ? PickUpDropOff::fromArray($originData) : null,
            PickUpDropOff::fromArray(Getter::requireArray($data, 'destination', 'Parcel')),
            Date::from(Getter::requireString($data, 'created_at', 'Parcel')),
            $expireAt !== null ? Date::from($expireAt) : null,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTracking(): string
    {
        return $this->tracking;
    }

    public function getQrTracking(): string
    {
        return $this->qrTracking;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getQrLabel(): ?string
    {
        return $this->qrLabel;
    }

    public function getContent(): ParcelContent
    {
        return $this->content;
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

    public function getSender(): Person
    {
        return $this->sender;
    }

    public function getReceiver(): Person
    {
        return $this->receiver;
    }

    public function getOrigin(): ?PickUpDropOff
    {
        return $this->origin;
    }

    public function getDestination(): PickUpDropOff
    {
        return $this->destination;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpireAt(): ?DateTimeImmutable
    {
        return $this->expireAt;
    }
}
