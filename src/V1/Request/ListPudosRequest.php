<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\Utils\Getter;
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

    /**
     * Flat query string parameters as strings (skips nested array keys from parse_str).
     *
     * @return array<string, string>
     */
    private static function queryStringParams(string $url): array
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $raw = [];
        parse_str(is_string($query) ? $query : '', $raw);
        $params = [];
        foreach ($raw as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public static function fromUrl(string $url): self
    {
        $params = self::queryStringParams($url);

        $coordinate = null;
        if (isset($params['latitude'], $params['longitude'])) {
            $coordinate = new Coordinate((float) $params['latitude'], (float) $params['longitude']);
        }

        $postalCode = Getter::optionalString($params, 'postal_code');
        $radiusKmStr = Getter::optionalString($params, 'radius_km');
        $cursorStr = Getter::optionalString($params, 'cursor');

        return new self(
            $coordinate,
            $postalCode,
            $radiusKmStr !== null ? (int) $radiusKmStr : null,
            $cursorStr !== null ? Pagination::from($cursorStr) : null
        );
    }
}
