<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Tests\Mock\MockHttpClient;
use PuntoPost\Sdk\V1\AuthApi;
use PuntoPost\Sdk\V1\MerchantApi;
use PuntoPost\Sdk\V1\PuntoPostClient;

class PuntoPostClientTest extends TestCase
{
    private PuntoPostClient $sut;

    protected function setUp(): void
    {
        $this->sut = new PuntoPostClient('https://api.example.com', new MockHttpClient());
    }

    public function testAuthReturnsAuthApi(): void
    {
        $this->assertInstanceOf(AuthApi::class, $this->sut->auth());
    }

    public function testMerchantReturnsMerchantApi(): void
    {
        $this->assertInstanceOf(MerchantApi::class, $this->sut->merchant());
    }
}
