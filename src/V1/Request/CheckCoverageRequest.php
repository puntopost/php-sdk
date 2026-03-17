<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

final class CheckCoverageRequest
{
    private string $postalCode;

    public function __construct(string $postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }
}
