<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request\DTO;

class ParcelContentData
{
    private string $description;
    private ?DeclaredValue $declaredValue;
    private ?string $imageUrl;
    private ?float $weightKg;

    public function __construct(
        string         $description,
        ?DeclaredValue $declaredValue = null,
        ?string        $imageUrl = null,
        ?float         $weightKg = null
    ) {
        $this->description = $description;
        $this->declaredValue = $declaredValue;
        $this->imageUrl = $imageUrl;
        $this->weightKg = $weightKg;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $data = ['description' => $this->description];

        if ($this->declaredValue !== null) {
            $data['value'] = $this->declaredValue->getValue();
            $data['currency'] = $this->declaredValue->getCurrency();
        }
        if ($this->imageUrl !== null) {
            $data['image_url'] = $this->imageUrl;
        }
        if ($this->weightKg !== null) {
            $data['weight_kg'] = $this->weightKg;
        }

        return $data;
    }
}
