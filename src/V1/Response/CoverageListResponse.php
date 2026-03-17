<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\Utils\Getter;

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
        $itemsRaw = Getter::requireArray($data, 'items', 'CoverageListResponse');

        return new self(array_map(
            fn ($item, $index): string => Getter::requireString(
                $item,
                null,
                sprintf('CoverageListResponse items[%s]', (string) $index)
            ),
            $itemsRaw,
            array_keys($itemsRaw)
        ));
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
