<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\GetMerchantRequest;

class GetMerchantRequestTest extends TestCase
{
    public function testConstructorStoresId(): void
    {
        $request = new GetMerchantRequest('MERCH-001');

        $this->assertSame('MERCH-001', $request->getId());
    }

    public function testWithEmptyId(): void
    {
        $request = new GetMerchantRequest('');

        $this->assertSame('', $request->getId());
    }

    public function testWithNumericStringId(): void
    {
        $request = new GetMerchantRequest('99');

        $this->assertSame('99', $request->getId());
    }

    public function testWithUuidStyleId(): void
    {
        $request = new GetMerchantRequest('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $request->getId());
    }
}
