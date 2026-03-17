<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\CoverageListResponse;

class CoverageListResponseTest extends TestCase
{
    public function testConstructorStoresPostalCodes(): void
    {
        $response = new CoverageListResponse(['06600', '64000', '01000']);

        $this->assertSame(['06600', '64000', '01000'], $response->getPostalCodes());
    }

    public function testConstructorWithEmptyArray(): void
    {
        $response = new CoverageListResponse([]);

        $this->assertSame([], $response->getPostalCodes());
    }

    public function testFromArrayWithValidItems(): void
    {
        $response = CoverageListResponse::fromArray(['items' => ['06600', '64000', '01000']]);

        $this->assertSame(['06600', '64000', '01000'], $response->getPostalCodes());
    }

    public function testFromArrayWithEmptyItemsList(): void
    {
        $response = CoverageListResponse::fromArray(['items' => []]);

        $this->assertSame([], $response->getPostalCodes());
    }

    public function testFromArrayWithEmptyArrayDefaultsToEmpty(): void
    {
        $response = CoverageListResponse::fromArray([]);

        $this->assertSame([], $response->getPostalCodes());
    }

    public function testFromArrayFiltersOutNonStringItems(): void
    {
        $response = CoverageListResponse::fromArray(['items' => ['06600', 99, null, true, ['06700'], '64000']]);

        $this->assertSame(['06600', '64000'], $response->getPostalCodes());
    }

    public function testFromArrayWithItemsNotArrayIsIgnored(): void
    {
        $response = CoverageListResponse::fromArray(['items' => 'not_an_array']);

        $this->assertSame([], $response->getPostalCodes());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = CoverageListResponse::fromArray(['postal_codes' => ['06600'], 'data' => 'x']);

        $this->assertSame([], $response->getPostalCodes());
    }

    public function testHasReturnsTrueForExistingPostalCode(): void
    {
        $response = new CoverageListResponse(['06600', '64000']);

        $this->assertTrue($response->has('06600'));
        $this->assertTrue($response->has('64000'));
    }

    public function testHasReturnsFalseForMissingPostalCode(): void
    {
        $response = new CoverageListResponse(['06600']);

        $this->assertFalse($response->has('99999'));
    }

    public function testHasIsCaseSensitive(): void
    {
        $response = new CoverageListResponse(['06600']);

        $this->assertFalse($response->has('06600 '));
        $this->assertFalse($response->has(''));
    }

    public function testHasOnEmptyListReturnsFalse(): void
    {
        $response = new CoverageListResponse([]);

        $this->assertFalse($response->has('06600'));
    }
}
