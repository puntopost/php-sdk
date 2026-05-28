<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\ParcelSummary;

class ParcelListResponse
{
    private int $total;
    /** @var ParcelSummary[] */
    private array $items;

    /**
     * @param ParcelSummary[] $items
     */
    public function __construct(int $total, array $items)
    {
        $this->total = $total;
        $this->items = $items;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $itemsRaw = Getter::requireArray($data, 'items', 'ParcelListResponse');
        $items = array_map(
            fn ($item, $index): ParcelSummary => ParcelSummary::fromArray(
                Getter::requireArray($item, null, sprintf('ParcelListResponse items[%s]', (string) $index))
            ),
            $itemsRaw,
            array_keys($itemsRaw)
        );

        return new self(
            Getter::requireInt($data, 'total', 'ParcelListResponse'),
            $items
        );
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return ParcelSummary[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
