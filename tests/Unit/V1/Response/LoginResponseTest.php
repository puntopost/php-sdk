<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\LoginResponse;

class LoginResponseTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $response = new LoginResponse('my.jwt.token', 3600);

        $this->assertSame('my.jwt.token', $response->getToken());
        $this->assertSame(3600, $response->getExpiresIn());
    }

    public function testFromArrayWithValidData(): void
    {
        $response = LoginResponse::fromArray(['token' => 'my.jwt.token', 'expires_in' => 3600]);

        $this->assertSame('my.jwt.token', $response->getToken());
        $this->assertSame(3600, $response->getExpiresIn());
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray([]);
    }

    public function testFromArrayWithWrongTypeForTokenThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray(['token' => 12345, 'expires_in' => 3600]);
    }

    public function testFromArrayWithWrongTypeForExpiresInThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray(['token' => 'abc', 'expires_in' => '3600']);
    }

    public function testFromArrayWithFloatExpiresInThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray(['token' => 'abc', 'expires_in' => 3600.5]);
    }

    public function testFromArrayWithNullValuesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray(['token' => null, 'expires_in' => null]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LoginResponse::fromArray(['username' => 'foo', 'password' => 'bar', 'status' => 200]);
    }
}
