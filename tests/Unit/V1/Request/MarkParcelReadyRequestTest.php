<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\MarkParcelReadyRequest;

class MarkParcelReadyRequestTest extends TestCase
{
    public function testConstructorStoresIdentifier(): void
    {
        $request = new MarkParcelReadyRequest('PARCEL-001');

        $this->assertSame('PARCEL-001', $request->getIdentifier());
    }

    public function testWithEmptyIdentifier(): void
    {
        $request = new MarkParcelReadyRequest('');

        $this->assertSame('', $request->getIdentifier());
    }

    public function testWithUuidStyleIdentifier(): void
    {
        $request = new MarkParcelReadyRequest('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $request->getIdentifier());
    }
}
