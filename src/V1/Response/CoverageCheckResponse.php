<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\Utils\Getter;

class CoverageCheckResponse
{
    private bool $covered;

    public function __construct(bool $covered)
    {
        $this->covered = $covered;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(Getter::requireBool($data, 'covered', 'CoverageCheckResponse'));
    }

    public function isCovered(): bool
    {
        return $this->covered;
    }
}
