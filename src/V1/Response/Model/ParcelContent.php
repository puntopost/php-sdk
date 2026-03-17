<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class ParcelContent
{
    private string $description;
    private ?float $weightKg;

    public function __construct(string $description, ?float $weightKg)
    {
        $this->description = $description;
        $this->weightKg = $weightKg;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Getter::requireString($data, 'description', 'ParcelContent'),
            Getter::optionalFloat($data, 'weight_kg')
        );
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWeightKg(): ?float
    {
        return $this->weightKg;
    }
}
