<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;

class PudoListResponse
{
    private Coordinate $coordinate;
    /** @var PickUpDropOff[] */
    private array $items;
    private ?ListPudosRequest $next;

    /**
     * @param PickUpDropOff[] $items
     */
    public function __construct(Coordinate $coordinate, array $items, ?ListPudosRequest $next)
    {
        $this->coordinate = $coordinate;
        $this->items = $items;
        $this->next = $next;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $coordinate = Coordinate::fromArray(Getter::requireArray($data, 'coordinate', 'PudoListResponse'));

        $itemsRaw = Getter::requireArray($data, 'items', 'PudoListResponse');
        $items = array_map(
            fn ($item, $index): PickUpDropOff => PickUpDropOff::fromArray(
                Getter::requireArray($item, null, sprintf('PudoListResponse items[%s]', (string) $index))
            ),
            $itemsRaw,
            array_keys($itemsRaw)
        );

        $nextUrl = Getter::optionalString($data, 'next');

        return new self(
            $coordinate,
            $items,
            $nextUrl !== null ? ListPudosRequest::fromUrl($nextUrl) : null
        );
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    /**
     * @return PickUpDropOff[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getNext(): ?ListPudosRequest
    {
        return $this->next;
    }
}
