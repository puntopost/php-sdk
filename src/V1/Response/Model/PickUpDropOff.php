<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;

class PickUpDropOff
{
    private string $id;
    private string $externalId;
    private string $name;
    private string $description;
    private Address $address;
    private string $schedule;
    private bool $enabled;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $externalId,
        string $name,
        string $description,
        Address $address,
        string $schedule,
        bool $enabled,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->name = $name;
        $this->description = $description;
        $this->address = $address;
        $this->schedule = $schedule;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Getter::requireString($data, 'id', 'PickUpDropOff'),
            Getter::requireString($data, 'external_id', 'PickUpDropOff'),
            Getter::requireString($data, 'name', 'PickUpDropOff'),
            Getter::requireString($data, 'description', 'PickUpDropOff'),
            Address::fromArray(Getter::requireArray($data, 'address', 'PickUpDropOff')),
            Getter::requireString($data, 'schedule', 'PickUpDropOff'),
            Getter::requireBool($data, 'enabled', 'PickUpDropOff'),
            Date::from(Getter::requireString($data, 'created_at', 'PickUpDropOff'))
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
