<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Enum\UserType;
use PuntoPost\Sdk\V1\Response\Model\User;

class UserTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $type = UserType::from(UserType::MERCHANT);
        $createdAt = new DateTimeImmutable('2024-01-15T00:00:00+00:00');
        $user = new User('UID1', 'jdoe', 'jdoe@example.com', $type, true, $createdAt);

        $this->assertSame('UID1', $user->getId());
        $this->assertSame('jdoe', $user->getUsername());
        $this->assertSame('jdoe@example.com', $user->getEmail());
        $this->assertSame('merchant', $user->getType()->getValue());
        $this->assertTrue($user->isEnabled());
        $this->assertSame($createdAt, $user->getCreatedAt());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $user = User::fromArray([
            'id' => 'UID1',
            'username' => 'jdoe',
            'email' => 'jdoe@example.com',
            'type' => 'merchant',
            'enabled' => true,
            'created_at' => '2024-01-15T00:00:00+00:00',
        ]);

        $this->assertSame('UID1', $user->getId());
        $this->assertSame('jdoe', $user->getUsername());
        $this->assertSame('jdoe@example.com', $user->getEmail());
        $this->assertSame('merchant', $user->getType()->getValue());
        $this->assertTrue($user->isEnabled());
        $this->assertSame('2024-01-15', $user->getCreatedAt()->format('Y-m-d'));
    }

    public function testFromArrayEnabledIsFalseWhenAbsentThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::fromArray(['id' => 'X', 'username' => 'u', 'email' => 'e@e.com', 'type' => 'staff', 'created_at' => '2024-01-01T00:00:00+00:00']);
    }

    public function testFromArrayEnabledIsFalseWhenNotBoolThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::fromArray(['id' => 'X', 'username' => 'u', 'email' => 'e@e.com', 'type' => 'staff', 'enabled' => 1, 'created_at' => '2024-01-01T00:00:00+00:00']);
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::fromArray([]);
    }

    public function testFromArrayWithWrongTypesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::fromArray([
            'id' => 99,
            'username' => null,
            'email' => ['jdoe@example.com'],
            'type' => true,
            'enabled' => 'yes',
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        User::fromArray(['x' => 1, 'y' => 2, 'z' => 3]);
    }
}
