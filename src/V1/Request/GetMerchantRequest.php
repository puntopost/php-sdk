<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

final class GetMerchantRequest
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
