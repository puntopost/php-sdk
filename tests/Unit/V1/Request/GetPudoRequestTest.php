<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\GetPudoRequest;

class GetPudoRequestTest extends TestCase
{
    public function testConstructorStoresId(): void
    {
        $request = new GetPudoRequest('PUDO_001');

        $this->assertSame('PUDO_001', $request->getId());
    }

    public function testWithEmptyId(): void
    {
        $request = new GetPudoRequest('');

        $this->assertSame('', $request->getId());
    }

    public function testWithNumericStringId(): void
    {
        $request = new GetPudoRequest('42');

        $this->assertSame('42', $request->getId());
    }

    public function testWithUuidStyleId(): void
    {
        $request = new GetPudoRequest('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $request->getId());
    }
}
