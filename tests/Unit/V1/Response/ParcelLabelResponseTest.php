<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\ParcelLabelResponse;

class ParcelLabelResponseTest extends TestCase
{
    public function testGetExtensionReturnsPdfForApplicationPdf(): void
    {
        $response = new ParcelLabelResponse('fake-pdf', 'application/pdf');

        $this->assertSame('pdf', $response->getExtension());
    }

    public function testGetExtensionReturnsPngForImagePng(): void
    {
        $response = new ParcelLabelResponse('fake-png', 'image/png');

        $this->assertSame('png', $response->getExtension());
    }

    public function testGetExtensionThrowsForUnknownContentType(): void
    {
        $response = new ParcelLabelResponse('whatever', 'application/octet-stream');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported parcel label content type: application/octet-stream');

        $response->getExtension();
    }
}
