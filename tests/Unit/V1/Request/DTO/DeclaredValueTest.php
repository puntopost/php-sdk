<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request\DTO;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\DeclaredValue;

class DeclaredValueTest extends TestCase
{
    public function testMxnFactoryStoresValueAndCurrency(): void
    {
        $declared = DeclaredValue::mxn(250.0);

        $this->assertSame(250.0, $declared->getValue());
        $this->assertSame('MXN', $declared->getCurrency());
    }

    public function testMxnConstantValue(): void
    {
        $this->assertSame('MXN', DeclaredValue::MXN);
    }

    public function testMxnWithZeroValue(): void
    {
        $declared = DeclaredValue::mxn(0.0);

        $this->assertSame(0.0, $declared->getValue());
        $this->assertSame('MXN', $declared->getCurrency());
    }

    public function testMxnWithHighPrecisionValue(): void
    {
        $declared = DeclaredValue::mxn(1999.99);

        $this->assertSame(1999.99, $declared->getValue());
    }

    public function testMxnWithLargeValue(): void
    {
        $declared = DeclaredValue::mxn(1000000.0);

        $this->assertSame(1000000.0, $declared->getValue());
        $this->assertSame('MXN', $declared->getCurrency());
    }
}
