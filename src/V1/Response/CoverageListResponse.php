<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

class CoverageListResponse
{
    /** @var string[] */
    private array $postalCodes;

    /**
     * @param string[] $postalCodes
     */
    public function __construct(array $postalCodes)
    {
        $this->postalCodes = $postalCodes;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $postalCodes = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (is_string($item)) {
                    $postalCodes[] = $item;
                }
            }
        }

        return new self($postalCodes);
    }

    /**
     * @return string[]
     */
    public function getPostalCodes(): array
    {
        return $this->postalCodes;
    }

    public function has(string $postalCode): bool
    {
        return in_array($postalCode, $this->postalCodes, true);
    }
}
