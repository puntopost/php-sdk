<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

class MerchantPickUpDropOff
{
    private string $id;
    private string $externalId;
    private string $type;
    private string $name;
    private Address $address;
    private string $phone;
    private string $schedule;

    public function __construct(
        string $id,
        string $externalId,
        string $type,
        string $name,
        Address $address,
        string $phone,
        string $schedule
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->type = $type;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
        $this->schedule = $schedule;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && is_string($data['id']) ? $data['id'] : '',
            isset($data['external_id']) && is_string($data['external_id']) ? $data['external_id'] : '',
            isset($data['type']) && is_string($data['type']) ? $data['type'] : '',
            isset($data['name']) && is_string($data['name']) ? $data['name'] : '',
            Address::fromArray(isset($data['address']) && is_array($data['address']) ? $data['address'] : []),
            isset($data['phone']) && is_string($data['phone']) ? $data['phone'] : '',
            isset($data['schedule']) && is_string($data['schedule']) ? $data['schedule'] : ''
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

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getSchedule(): string
    {
        return $this->schedule;
    }
}
