<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class Coordinate
{
    private float $latitude;
    private float $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Getter::requireFloat($data, 'latitude', 'Coordinate'),
            Getter::requireFloat($data, 'longitude', 'Coordinate')
        );
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}
