<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\MerchantDetailResponse;
use PuntoPost\Sdk\V1\Response\Model\Merchant;

class MerchantDetailResponseTest extends TestCase
{
    public function testConstructorStoresDetail(): void
    {
        $merchant = new Merchant('M1', 'Tienda', true, false, null, '2024-01-01', [], []);
        $response = new MerchantDetailResponse($merchant);

        $this->assertSame($merchant, $response->getDetail());
    }

    public function testFromArrayWithValidPayload(): void
    {
        $response = MerchantDetailResponse::fromArray([
            'detail' => [
                'id' => 'M1', 'name' => 'Tienda', 'enabled' => true,
                'webhook_enabled' => false, 'created_at' => '2024-01-01', 'users' => [], 'pudos' => [],
            ],
        ]);

        $this->assertSame('M1', $response->getDetail()->getId());
        $this->assertSame('Tienda', $response->getDetail()->getName());
    }

    public function testFromArrayWithEmptyArrayProducesDefaultMerchant(): void
    {
        $response = MerchantDetailResponse::fromArray([]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNotArrayProducesDefaultMerchant(): void
    {
        $response = MerchantDetailResponse::fromArray(['detail' => 'not_an_array']);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithDetailNullProducesDefaultMerchant(): void
    {
        $response = MerchantDetailResponse::fromArray(['detail' => null]);

        $this->assertSame('', $response->getDetail()->getId());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = MerchantDetailResponse::fromArray(['merchant' => 'M1', 'status' => 'ok']);

        $this->assertSame('', $response->getDetail()->getId());
    }
}
