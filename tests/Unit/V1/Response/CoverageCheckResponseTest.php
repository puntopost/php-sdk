<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\CoverageCheckResponse;

class CoverageCheckResponseTest extends TestCase
{
    public function testConstructorWithTrueStoresTrue(): void
    {
        $response = new CoverageCheckResponse(true);

        $this->assertTrue($response->isCovered());
    }

    public function testConstructorWithFalseStoresFalse(): void
    {
        $response = new CoverageCheckResponse(false);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithCoveredTrue(): void
    {
        $response = CoverageCheckResponse::fromArray(['covered' => true]);

        $this->assertTrue($response->isCovered());
    }

    public function testFromArrayWithCoveredFalse(): void
    {
        $response = CoverageCheckResponse::fromArray(['covered' => false]);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithEmptyArrayDefaultsToFalse(): void
    {
        $response = CoverageCheckResponse::fromArray([]);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithStringTrueIsNotCovered(): void
    {
        $response = CoverageCheckResponse::fromArray(['covered' => 'true']);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithIntegerOneIsNotCovered(): void
    {
        $response = CoverageCheckResponse::fromArray(['covered' => 1]);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithNullIsNotCovered(): void
    {
        $response = CoverageCheckResponse::fromArray(['covered' => null]);

        $this->assertFalse($response->isCovered());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = CoverageCheckResponse::fromArray(['postal_code' => '06600', 'region' => 'CDMX']);

        $this->assertFalse($response->isCovered());
    }
}
