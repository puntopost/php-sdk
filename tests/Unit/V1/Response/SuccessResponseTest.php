<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\SuccessResponse;

class SuccessResponseTest extends TestCase
{
    public function testConstructorStoresStatusCode(): void
    {
        $response = new SuccessResponse(204);

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testIsSuccessForCode200(): void
    {
        $this->assertTrue((new SuccessResponse(200))->isSuccess());
    }

    public function testIsSuccessForCode201(): void
    {
        $this->assertTrue((new SuccessResponse(201))->isSuccess());
    }

    public function testIsSuccessForCode204(): void
    {
        $this->assertTrue((new SuccessResponse(204))->isSuccess());
    }

    public function testIsSuccessForCode299(): void
    {
        $this->assertTrue((new SuccessResponse(299))->isSuccess());
    }

    public function testIsNotSuccessForCode300(): void
    {
        $this->assertFalse((new SuccessResponse(300))->isSuccess());
    }

    public function testIsNotSuccessForCode400(): void
    {
        $this->assertFalse((new SuccessResponse(400))->isSuccess());
    }

    public function testIsNotSuccessForCode500(): void
    {
        $this->assertFalse((new SuccessResponse(500))->isSuccess());
    }

    public function testIsNotSuccessForCode199(): void
    {
        $this->assertFalse((new SuccessResponse(199))->isSuccess());
    }

    public function testIsNotSuccessForCode0(): void
    {
        $this->assertFalse((new SuccessResponse(0))->isSuccess());
    }
}
