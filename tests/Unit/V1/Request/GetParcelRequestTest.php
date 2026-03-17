<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\GetParcelRequest;

class GetParcelRequestTest extends TestCase
{
    public function testConstructorStoresIdentifier(): void
    {
        $request = new GetParcelRequest('PARCEL-001');

        $this->assertSame('PARCEL-001', $request->getIdentifier());
    }

    public function testWithUuidStyleIdentifier(): void
    {
        $request = new GetParcelRequest('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $request->getIdentifier());
    }

    public function testWithEmptyIdentifier(): void
    {
        $request = new GetParcelRequest('');

        $this->assertSame('', $request->getIdentifier());
    }

    public function testWithNumericStringIdentifier(): void
    {
        $request = new GetParcelRequest('12345');

        $this->assertSame('12345', $request->getIdentifier());
    }

    public function testWithSpecialCharactersInIdentifier(): void
    {
        $request = new GetParcelRequest('MX/2024-ABC_001');

        $this->assertSame('MX/2024-ABC_001', $request->getIdentifier());
    }
}
