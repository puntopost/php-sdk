<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

class ParcelSummary
{
    private string $id;
    private string $tracking;
    private ?string $label;
    private ParcelContent $content;
    private ParcelStatus $status;
    private Person $sender;
    private Person $receiver;
    private ?PickUpDropOff $origin;
    private PickUpDropOff $destination;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $expireAt;

    public function __construct(
        string $id,
        string $tracking,
        ?string $label,
        ParcelContent $content,
        ParcelStatus $status,
        Person $sender,
        Person $receiver,
        ?PickUpDropOff $origin,
        PickUpDropOff $destination,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $expireAt
    ) {
        $this->id = $id;
        $this->tracking = $tracking;
        $this->label = $label;
        $this->content = $content;
        $this->status = $status;
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
        $originData = Getter::optionalArray($data, 'origin');
        $expireAt = Getter::optionalString($data, 'expire_at');

        return new self(
            Getter::requireString($data, 'id', 'ParcelSummary'),
            Getter::requireString($data, 'tracking', 'ParcelSummary'),
            Getter::optionalString($data, 'label'),
            ParcelContent::fromArray(Getter::requireArray($data, 'content', 'ParcelSummary')),
            ParcelStatus::from(Getter::requireString($data, 'status', 'ParcelSummary')),
            Person::fromArray(Getter::requireArray($data, 'sender', 'ParcelSummary')),
            Person::fromArray(Getter::requireArray($data, 'receiver', 'ParcelSummary')),
            $originData !== null ? PickUpDropOff::fromArray($originData) : null,
            PickUpDropOff::fromArray(Getter::requireArray($data, 'destination', 'ParcelSummary')),
            Date::from(Getter::requireString($data, 'created_at', 'ParcelSummary')),
            $expireAt !== null ? Date::from($expireAt) : null
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getContent(): ParcelContent
    {
        return $this->content;
    }

    public function getStatus(): ParcelStatus
    {
        return $this->status;
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
