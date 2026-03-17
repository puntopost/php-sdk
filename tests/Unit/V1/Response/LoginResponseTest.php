<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

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

    public function testFromArrayWithEmptyArrayDefaultsToEmpty(): void
    {
        $response = LoginResponse::fromArray([]);

        $this->assertSame('', $response->getToken());
        $this->assertSame(0, $response->getExpiresIn());
    }

    public function testFromArrayWithWrongTypeForTokenDefaultsToEmpty(): void
    {
        $response = LoginResponse::fromArray(['token' => 12345, 'expires_in' => 3600]);

        $this->assertSame('', $response->getToken());
        $this->assertSame(3600, $response->getExpiresIn());
    }

    public function testFromArrayWithWrongTypeForExpiresInDefaultsToZero(): void
    {
        $response = LoginResponse::fromArray(['token' => 'abc', 'expires_in' => '3600']);

        $this->assertSame('abc', $response->getToken());
        $this->assertSame(0, $response->getExpiresIn());
    }

    public function testFromArrayWithFloatExpiresInDefaultsToZero(): void
    {
        $response = LoginResponse::fromArray(['token' => 'abc', 'expires_in' => 3600.5]);

        $this->assertSame(0, $response->getExpiresIn());
    }

    public function testFromArrayWithNullValuesDefaultToEmpty(): void
    {
        $response = LoginResponse::fromArray(['token' => null, 'expires_in' => null]);

        $this->assertSame('', $response->getToken());
        $this->assertSame(0, $response->getExpiresIn());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $response = LoginResponse::fromArray(['username' => 'foo', 'password' => 'bar', 'status' => 200]);

        $this->assertSame('', $response->getToken());
        $this->assertSame(0, $response->getExpiresIn());
    }
}
