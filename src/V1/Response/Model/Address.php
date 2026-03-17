<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class Address
{
    private string $postalCode;
    private string $city;
    private string $address;
    private Coordinate $coordinate;

    public function __construct(
        string $postalCode,
        string $city,
        string $address,
        Coordinate $coordinate
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
        return new self(
            Getter::requireString($data, 'postal_code', 'Address'),
            Getter::requireString($data, 'city', 'Address'),
            Getter::requireString($data, 'address', 'Address'),
            Coordinate::fromArray(Getter::requireArray($data, 'coordinate', 'Address'))
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

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }
}
