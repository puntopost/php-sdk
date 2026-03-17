<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\Model\Enum\UserType;
use PuntoPost\Sdk\V1\Response\Model\Merchant;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;
use PuntoPost\Sdk\V1\Response\Model\User;

class MerchantTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $user = new User('UID1', 'jdoe', 'j@e.com', UserType::from('merchant'), true, new DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $pudo = new PickUpDropOff('P1', 'EP1', 'Central', '', new Address('06600', 'CDMX', 'Calle 1', new Coordinate(19.4326, -99.1332)), 'Lun-Vie', true, new DateTimeImmutable('2024-01-01T00:00:00+00:00'));
        $merchant = new Merchant('MID1', 'Mi Tienda', true, false, null, '2024-01-01T00:00:00+00:00', [$user], [$pudo]);

        $this->assertSame('MID1', $merchant->getId());
        $this->assertSame('Mi Tienda', $merchant->getName());
        $this->assertTrue($merchant->isEnabled());
        $this->assertFalse($merchant->isWebhookEnabled());
        $this->assertNull($merchant->getWebhookUrl());
        $this->assertCount(1, $merchant->getUsers());
        $this->assertCount(1, $merchant->getPudos());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $merchant = Merchant::fromArray([
            'id' => 'MID1',
            'name' => 'Mi Tienda',
            'enabled' => true,
            'webhook_enabled' => true,
            'webhook_url' => 'https://example.com/webhook',
            'created_at' => '2024-01-01T00:00:00+00:00',
            'users' => [
                ['id' => 'UID1', 'username' => 'jdoe', 'email' => 'j@e.com', 'type' => 'merchant', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
            ],
            'pudos' => [
                ['id' => 'P1', 'external_id' => 'EP1', 'name' => 'Central', 'description' => '', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]], 'schedule' => 'Lun-Vie', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
            ],
        ]);

        $this->assertSame('MID1', $merchant->getId());
        $this->assertSame('Mi Tienda', $merchant->getName());
        $this->assertTrue($merchant->isEnabled());
        $this->assertTrue($merchant->isWebhookEnabled());
        $this->assertSame('https://example.com/webhook', $merchant->getWebhookUrl());
        $this->assertCount(1, $merchant->getUsers());
        $this->assertSame('jdoe', $merchant->getUsers()[0]->getUsername());
        $this->assertCount(1, $merchant->getPudos());
        $this->assertSame('P1', $merchant->getPudos()[0]->getId());
    }

    public function testFromArrayWithoutWebhookUrlIsNull(): void
    {
        $merchant = Merchant::fromArray(['id' => 'M1', 'name' => 'T', 'enabled' => true, 'webhook_enabled' => false, 'created_at' => '2024-01-01T00:00:00+00:00', 'users' => [], 'pudos' => []]);

        $this->assertNull($merchant->getWebhookUrl());
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Merchant::fromArray([]);
    }

    public function testFromArrayNonArrayUserThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid value in Merchant users[0] (expected array)');

        Merchant::fromArray([
            'id' => 'M1',
            'name' => 'T',
            'enabled' => true,
            'webhook_enabled' => false,
            'created_at' => '2024-01-01T00:00:00+00:00',
            'users' => ['not_array_1', 99, null, true],
            'pudos' => [],
        ]);
    }

    public function testFromArrayNonArrayPudoThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid value in Merchant pudos[0] (expected array)');

        Merchant::fromArray([
            'id' => 'M1',
            'name' => 'T',
            'enabled' => true,
            'webhook_enabled' => false,
            'created_at' => '2024-01-01T00:00:00+00:00',
            'users' => [],
            'pudos' => ['string_pudo', 42, false],
        ]);
    }

    public function testFromArrayUsersBuildRecursively(): void
    {
        $merchant = Merchant::fromArray([
            'id' => 'M1', 'name' => 'T', 'enabled' => true, 'webhook_enabled' => false, 'created_at' => '2024-01-01T00:00:00+00:00',
            'users' => [
                ['id' => 'U1', 'username' => 'alice', 'email' => 'a@e.com', 'type' => 'staff', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
                ['id' => 'U2', 'username' => 'bob', 'email' => 'b@e.com', 'type' => 'operator', 'enabled' => false, 'created_at' => '2024-01-02T00:00:00+00:00'],
            ],
            'pudos' => [],
        ]);

        $this->assertCount(2, $merchant->getUsers());
        $this->assertSame('alice', $merchant->getUsers()[0]->getUsername());
        $this->assertSame('bob', $merchant->getUsers()[1]->getUsername());
    }

    public function testFromArrayWithWrongTypesForScalarFieldsThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Merchant::fromArray([
            'id' => ['array_id'],
            'name' => 999,
            'enabled' => 'yes',
            'webhook_enabled' => 1,
            'webhook_url' => 12345,
            'created_at' => '2024-01-01',
            'users' => [],
            'pudos' => [],
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Merchant::fromArray(['alpha' => 1, 'beta' => 'two', 'gamma' => [3]]);
    }
}
