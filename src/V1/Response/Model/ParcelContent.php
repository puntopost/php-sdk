<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class ParcelContent
{
    private string $description;
    private ?float $weightKg;
    private ?string $imageUrl;
    private ?DeclaredValue $declaredValue;

    public function __construct(string $description, ?float $weightKg, ?string $imageUrl, ?DeclaredValue $declaredValue)
    {
        $this->description = $description;
        $this->weightKg = $weightKg;
        $this->imageUrl = $imageUrl;
        $this->declaredValue = $declaredValue;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $declaredValueData = Getter::optionalArray($data, 'declared_value');

        return new self(
            Getter::requireString($data, 'description', 'ParcelContent'),
            Getter::optionalFloat($data, 'weight_kg'),
            Getter::optionalString($data, 'image_url'),
            $declaredValueData !== null ? DeclaredValue::fromArray($declaredValueData) : null
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getDeclaredValue(): ?DeclaredValue
    {
        return $this->declaredValue;
    }
}
