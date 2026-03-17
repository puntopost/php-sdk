<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;

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
            isset($data['id']) && is_string($data['id']) ? $data['id'] : '',
            isset($data['external_id']) && is_string($data['external_id']) ? $data['external_id'] : '',
            isset($data['name']) && is_string($data['name']) ? $data['name'] : '',
            isset($data['description']) && is_string($data['description']) ? $data['description'] : '',
            Address::fromArray(isset($data['address']) && is_array($data['address']) ? $data['address'] : []),
            isset($data['schedule']) && is_string($data['schedule']) ? $data['schedule'] : '',
            isset($data['enabled']) && is_bool($data['enabled']) && $data['enabled'],
            Date::from(isset($data['created_at']) && is_string($data['created_at']) ? $data['created_at'] : '')
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
