<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

final class MarkParcelReadyRequest
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
