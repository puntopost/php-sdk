<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
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

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MerchantDetailResponse::fromArray([]);
    }

    public function testFromArrayWithDetailNotArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MerchantDetailResponse::fromArray(['detail' => 'not_an_array']);
    }

    public function testFromArrayWithDetailNullThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MerchantDetailResponse::fromArray(['detail' => null]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        MerchantDetailResponse::fromArray(['merchant' => 'M1', 'status' => 'ok']);
    }
}
