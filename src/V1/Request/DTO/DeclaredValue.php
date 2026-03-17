<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request\DTO;

class DeclaredValue
{
    public const MXN = 'MXN';

    private float $value;
    private string $currency;

    private function __construct(float $value, string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public static function mxn(float $value): self
    {
        return new self($value, self::MXN);
    }
}
