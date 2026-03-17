<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model\Enum;

class UserType
{
    public const STAFF = 'staff';
    public const MERCHANT = 'merchant';
    public const PUDOS = 'pudos';
    public const CONTROL_TOWER = 'control_tower';
    public const OPERATOR = 'operator';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isStaff(): bool
    {
        return $this->value === self::STAFF;
    }

    public function isMerchant(): bool
    {
        return $this->value === self::MERCHANT;
    }

    public function isPudos(): bool
    {
        return $this->value === self::PUDOS;
    }

    public function isOperator(): bool
    {
        return $this->value === self::OPERATOR;
    }

    public function isControlTower(): bool
    {
        return $this->value === self::CONTROL_TOWER;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }
}
