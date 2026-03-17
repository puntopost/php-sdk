<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

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
            isset($data['latitude']) && (is_float($data['latitude']) || is_int($data['latitude'])) ? (float) $data['latitude'] : 0.0,
            isset($data['longitude']) && (is_float($data['longitude']) || is_int($data['longitude'])) ? (float) $data['longitude'] : 0.0
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
