<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\CheckCoverageRequest;

class CheckCoverageRequestTest extends TestCase
{
    public function testConstructorStoresPostalCode(): void
    {
        $request = new CheckCoverageRequest('06600');

        $this->assertSame('06600', $request->getPostalCode());
    }

    public function testWithEmptyPostalCode(): void
    {
        $request = new CheckCoverageRequest('');

        $this->assertSame('', $request->getPostalCode());
    }

    public function testWithNumericPostalCode(): void
    {
        $request = new CheckCoverageRequest('64000');

        $this->assertSame('64000', $request->getPostalCode());
    }

    public function testLeadingZeroIsPreserved(): void
    {
        $request = new CheckCoverageRequest('01000');

        $this->assertSame('01000', $request->getPostalCode());
    }

    public function testWithAlphanumericPostalCode(): void
    {
        $request = new CheckCoverageRequest('W1A 1AA');

        $this->assertSame('W1A 1AA', $request->getPostalCode());
    }
}
