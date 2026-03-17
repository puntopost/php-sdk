<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\LoginRequest;

class LoginRequestTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $request = new LoginRequest('user@example.com', 's3cr3t');

        $this->assertSame('user@example.com', $request->getUsername());
        $this->assertSame('s3cr3t', $request->getPassword());
    }

    public function testToArrayReturnsExpectedStructure(): void
    {
        $request = new LoginRequest('user@example.com', 's3cr3t');

        $this->assertSame([
            'username' => 'user@example.com',
            'password' => 's3cr3t',
        ], $request->toArray());
    }

    public function testToArrayWithEmptyCredentials(): void
    {
        $request = new LoginRequest('', '');

        $this->assertSame([
            'username' => '',
            'password' => '',
        ], $request->toArray());
    }

    public function testToArrayKeysAreSnakeCase(): void
    {
        $request = new LoginRequest('u', 'p');
        $result = $request->toArray();

        $this->assertArrayHasKey('username', $result);
        $this->assertArrayHasKey('password', $result);
        $this->assertCount(2, $result);
    }

    public function testToArrayWithSpecialCharacters(): void
    {
        $request = new LoginRequest('user+tag@example.mx', 'P@$$w0rd!#%');

        $this->assertSame('user+tag@example.mx', $request->toArray()['username']);
        $this->assertSame('P@$$w0rd!#%', $request->toArray()['password']);
    }
}
