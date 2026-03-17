<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request\DTO;

class Pagination
{
    private int $offset;
    private int $limit;

    public function __construct(int $offset, int $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function __toString(): string
    {
        return $this->limit . '-' . $this->offset;
    }

    public static function from(string $cursor): ?self
    {
        $parts = explode('-', $cursor);
        if (count($parts) !== 2) {
            return null;
        }
        return new self((int) $parts[1], (int) $parts[0]);
    }
}
