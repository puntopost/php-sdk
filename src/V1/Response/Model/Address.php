<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

class Address
{
    private string $postalCode;
    private string $city;
    private string $address;
    private ?Coordinate $coordinate;

    public function __construct(
        string $postalCode,
        string $city,
        string $address,
        ?Coordinate $coordinate
    ) {
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->address = $address;
        $this->coordinate = $coordinate;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $coordinate = null;
        if (isset($data['coordinate']) && is_array($data['coordinate'])) {
            $coordinate = Coordinate::fromArray($data['coordinate']);
        }

        return new self(
            isset($data['postal_code']) && is_string($data['postal_code']) ? $data['postal_code'] : '',
            isset($data['city']) && is_string($data['city']) ? $data['city'] : '',
            isset($data['address']) && is_string($data['address']) ? $data['address'] : '',
            $coordinate
        );
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCoordinate(): ?Coordinate
    {
        return $this->coordinate;
    }
}
