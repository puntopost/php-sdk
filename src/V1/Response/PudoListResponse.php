<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

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
        $coordinate = Coordinate::fromArray(
            isset($data['coordinate']) && is_array($data['coordinate']) ? $data['coordinate'] : []
        );

        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (is_array($item)) {
                    $items[] = PickUpDropOff::fromArray($item);
                }
            }
        }

        $nextUrl = isset($data['next']) && is_string($data['next']) ? $data['next'] : null;

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
