<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

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
            isset($data['description']) && is_string($data['description']) ? $data['description'] : '',
            isset($data['weight_kg']) && (is_float($data['weight_kg']) || is_int($data['weight_kg'])) ? (float) $data['weight_kg'] : null
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
