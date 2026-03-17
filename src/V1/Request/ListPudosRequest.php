<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\V1\Request\DTO\Coordinate;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;

class ListPudosRequest
{
    private ?Coordinate $coordinate;
    private ?string $postalCode;
    private ?int $radiusKm;
    private ?Pagination $cursor;

    public function __construct(
        ?Coordinate $coordinate = null,
        ?string $postalCode = null,
        ?int $radiusKm = null,
        ?Pagination $cursor = null
    ) {
        $this->coordinate = $coordinate;
        $this->postalCode = $postalCode;
        $this->radiusKm = $radiusKm;
        $this->cursor = $cursor;
    }

    /**
     * @return array<string,mixed>
     */
    public function toQueryParams(): array
    {
        $params = [];

        if ($this->coordinate !== null) {
            $params['latitude'] = $this->coordinate->getLatitude();
            $params['longitude'] = $this->coordinate->getLongitude();
        }
        if ($this->postalCode !== null) {
            $params['postal_code'] = $this->postalCode;
        }
        if ($this->radiusKm !== null) {
            $params['radius_km'] = $this->radiusKm;
        }
        if ($this->cursor !== null) {
            $params['cursor'] = (string) $this->cursor;
        }

        return $params;
    }

    public static function byCoordinate(Coordinate $coordinate, ?int $radiusKm = null, ?Pagination $cursor = null): self
    {
        return new self($coordinate, null, $radiusKm, $cursor);
    }

    public static function byPostalCode(string $postalCode, ?int $radiusKm = null, ?Pagination $cursor = null): self
    {
        return new self(null, $postalCode, $radiusKm, $cursor);
    }

    public static function fromUrl(string $url): self
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str(is_string($query) ? $query : '', $params);

        $coordinate = null;
        if (isset($params['latitude'], $params['longitude'])) {
            $coordinate = new Coordinate((float) $params['latitude'], (float) $params['longitude']);
        }

        return new self(
            $coordinate,
            isset($params['postal_code']) && is_string($params['postal_code']) ? $params['postal_code'] : null,
            isset($params['radius_km']) && is_string($params['radius_km']) ? (int) $params['radius_km'] : null,
            isset($params['cursor']) && is_string($params['cursor']) ? Pagination::from($params['cursor']) : null
        );
    }
}
