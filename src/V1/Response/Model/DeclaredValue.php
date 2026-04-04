<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class DeclaredValue
{
    private float $value;
    private string $currency;

    public function __construct(float $value, string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Getter::requireFloat($data, 'value', 'DeclaredValue'),
            Getter::requireString($data, 'currency', 'DeclaredValue')
        );
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
