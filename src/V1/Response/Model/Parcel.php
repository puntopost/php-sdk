<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
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
        $statusHistory = [];
        if (isset($data['status_history']) && is_array($data['status_history'])) {
            foreach ($data['status_history'] as $entry) {
                if (is_array($entry)) {
                    $statusHistory[] = StatusHistoryEntry::fromArray($entry);
                }
            }
        }

        $origin = null;
        if (isset($data['origin']) && is_array($data['origin'])) {
            $origin = PickUpDropOff::fromArray($data['origin']);
        }

        return new self(
            isset($data['id']) && is_string($data['id']) ? $data['id'] : '',
            isset($data['tracking']) && is_string($data['tracking']) ? $data['tracking'] : '',
            isset($data['qr_tracking']) && is_string($data['qr_tracking']) ? $data['qr_tracking'] : '',
            isset($data['label']) && is_string($data['label']) ? $data['label'] : null,
            isset($data['qr_label']) && is_string($data['qr_label']) ? $data['qr_label'] : null,
            ParcelContent::fromArray(isset($data['content']) && is_array($data['content']) ? $data['content'] : []),
            ParcelStatus::from(isset($data['status']) && is_string($data['status']) ? $data['status'] : ''),
            $statusHistory,
            Person::fromArray(isset($data['sender']) && is_array($data['sender']) ? $data['sender'] : []),
            Person::fromArray(isset($data['receiver']) && is_array($data['receiver']) ? $data['receiver'] : []),
            $origin,
            PickUpDropOff::fromArray(isset($data['destination']) && is_array($data['destination']) ? $data['destination'] : []),
            Date::from(isset($data['created_at']) && is_string($data['created_at']) ? $data['created_at'] : ''),
            isset($data['expire_at']) && is_string($data['expire_at']) ? Date::from($data['expire_at']) : null,
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
