<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
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

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CoverageCheckResponse::fromArray([]);
    }

    public function testFromArrayWithStringTrueThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CoverageCheckResponse::fromArray(['covered' => 'true']);
    }

    public function testFromArrayWithIntegerOneThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CoverageCheckResponse::fromArray(['covered' => 1]);
    }

    public function testFromArrayWithNullThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CoverageCheckResponse::fromArray(['covered' => null]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        CoverageCheckResponse::fromArray(['postal_code' => '06600', 'region' => 'CDMX']);
    }
}
