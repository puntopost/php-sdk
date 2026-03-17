<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\Coordinate;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;
use PuntoPost\Sdk\V1\Request\ListPudosRequest;

class ListPudosRequestTest extends TestCase
{
    // --- toQueryParams ---

    public function testToQueryParamsWithNoParamsReturnsEmptyArray(): void
    {
        $request = new ListPudosRequest();

        $this->assertSame([], $request->toQueryParams());
    }

    public function testToQueryParamsWithCoordinateOnly(): void
    {
        $request = new ListPudosRequest(new Coordinate(19.4326, -99.1332));

        $this->assertSame([
            'latitude' => 19.4326,
            'longitude' => -99.1332,
        ], $request->toQueryParams());
    }

    public function testToQueryParamsWithPostalCodeOnly(): void
    {
        $request = new ListPudosRequest(null, '06600');

        $this->assertSame(['postal_code' => '06600'], $request->toQueryParams());
    }

    public function testToQueryParamsWithRadiusKmOnly(): void
    {
        $request = new ListPudosRequest(null, null, 10);

        $this->assertSame(['radius_km' => 10], $request->toQueryParams());
    }

    public function testToQueryParamsWithCursorOnly(): void
    {
        $request = new ListPudosRequest(null, null, null, new Pagination(0, 5));

        $this->assertSame(['cursor' => '5-0'], $request->toQueryParams());
    }

    public function testToQueryParamsWithAllParams(): void
    {
        $request = new ListPudosRequest(new Coordinate(19.4326, -99.1332), '06600', 5, new Pagination(10, 5));

        $this->assertSame([
            'latitude' => 19.4326,
            'longitude' => -99.1332,
            'postal_code' => '06600',
            'radius_km' => 5,
            'cursor' => '5-10',
        ], $request->toQueryParams());
    }

    public function testToQueryParamsDoesNotIncludeNullValues(): void
    {
        $request = new ListPudosRequest(null, '06600', null, null);

        $result = $request->toQueryParams();

        $this->assertArrayNotHasKey('latitude', $result);
        $this->assertArrayNotHasKey('longitude', $result);
        $this->assertArrayNotHasKey('radius_km', $result);
        $this->assertArrayNotHasKey('cursor', $result);
    }

    // --- fromUrl ---

    public function testFromUrlWithAllKnownParams(): void
    {
        $url = 'https://api.example.com/api/merchant/v1/pudos?latitude=19.4326&longitude=-99.1332&postal_code=06600&radius_km=5&cursor=10-5';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertSame(19.4326, $params['latitude']);
        $this->assertSame(-99.1332, $params['longitude']);
        $this->assertSame('06600', $params['postal_code']);
        $this->assertSame(5, $params['radius_km']);
        $this->assertSame('10-5', $params['cursor']);
    }

    public function testFromUrlWithPostalCodeAndRadiusOnly(): void
    {
        $url = 'https://api.example.com/pudos?postal_code=06600&radius_km=5';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertSame('06600', $params['postal_code']);
        $this->assertSame(5, $params['radius_km']);
        $this->assertArrayNotHasKey('latitude', $params);
        $this->assertArrayNotHasKey('longitude', $params);
        $this->assertArrayNotHasKey('cursor', $params);
    }

    public function testFromUrlWithCoordinateOnly(): void
    {
        $url = 'https://api.example.com/pudos?latitude=19.4326&longitude=-99.1332';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertSame(19.4326, $params['latitude']);
        $this->assertSame(-99.1332, $params['longitude']);
        $this->assertCount(2, $params);
    }

    public function testFromUrlWithEmptyStringProducesAllNullParams(): void
    {
        $request = ListPudosRequest::fromUrl('');

        $this->assertSame([], $request->toQueryParams());
    }

    public function testFromUrlWithNoQueryStringProducesAllNullParams(): void
    {
        $request = ListPudosRequest::fromUrl('https://api.example.com/pudos');

        $this->assertSame([], $request->toQueryParams());
    }

    public function testFromUrlWithCompletelyUnrelatedParamsProducesEmptyParams(): void
    {
        $request = ListPudosRequest::fromUrl('https://google.com/search?q=hello+world&hl=es&num=10');

        $this->assertSame([], $request->toQueryParams());
    }

    public function testFromUrlIgnoresExtraUnknownParams(): void
    {
        $url = 'https://api.example.com/pudos?postal_code=06600&foo=bar&unknown=123';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertSame('06600', $params['postal_code']);
        $this->assertArrayNotHasKey('foo', $params);
        $this->assertArrayNotHasKey('unknown', $params);
    }

    public function testFromUrlWithOnlyLatitudeProducesNoCoordinate(): void
    {
        $url = 'https://api.example.com/pudos?latitude=19.4326';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertArrayNotHasKey('latitude', $params);
        $this->assertArrayNotHasKey('longitude', $params);
    }

    public function testFromUrlWithOnlyLongitudeProducesNoCoordinate(): void
    {
        $url = 'https://api.example.com/pudos?longitude=-99.1332';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertArrayNotHasKey('latitude', $params);
        $this->assertArrayNotHasKey('longitude', $params);
    }

    public function testFromUrlWithMalformedCursorProducesNoCursor(): void
    {
        $url = 'https://api.example.com/pudos?postal_code=06600&cursor=invalid';
        $request = ListPudosRequest::fromUrl($url);

        $params = $request->toQueryParams();

        $this->assertSame('06600', $params['postal_code']);
        $this->assertArrayNotHasKey('cursor', $params);
    }

    public function testFromUrlWithCursorHavingTooManyPartsMakesItNull(): void
    {
        $url = 'https://api.example.com/pudos?cursor=5-0-extra';
        $request = ListPudosRequest::fromUrl($url);

        $this->assertArrayNotHasKey('cursor', $request->toQueryParams());
    }

    public function testFromUrlPreservesRadiusKmAsInteger(): void
    {
        $request = ListPudosRequest::fromUrl('https://api.example.com/pudos?radius_km=15');

        $params = $request->toQueryParams();

        $this->assertSame(15, $params['radius_km']);
        $this->assertIsInt($params['radius_km']);
    }

    public function testFromUrlPreservesFloatCoordinates(): void
    {
        $request = ListPudosRequest::fromUrl('https://api.example.com/pudos?latitude=19.432607530148446&longitude=-99.13319396972656');

        $params = $request->toQueryParams();

        $this->assertEqualsWithDelta(19.432607530148446, $params['latitude'], 0.000001);
        $this->assertEqualsWithDelta(-99.13319396972656, $params['longitude'], 0.000001);
    }
}
