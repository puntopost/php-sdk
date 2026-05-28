<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\V1\Request\GetParcelLabelRequest;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Coordinate;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;
use PuntoPost\Sdk\V1\Response\Model\Parcel;
use PuntoPost\Sdk\V1\Response\Model\ParcelContent;
use PuntoPost\Sdk\V1\Response\Model\Person;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;

class GetParcelLabelRequestTest extends TestCase
{
    public function testConstructorStoresIdentifier(): void
    {
        $request = new GetParcelLabelRequest('MXL0000000001');

        $this->assertSame('MXL0000000001', $request->getIdentifier());
    }

    public function testFromParcelUsesLabelIdentifier(): void
    {
        $parcel = $this->buildParcel('MXL0000000001');

        $request = GetParcelLabelRequest::fromParcel($parcel);

        $this->assertSame('MXL0000000001', $request->getIdentifier());
    }

    public function testFromParcelResponseUsesLabelIdentifier(): void
    {
        $response = new ParcelDetailResponse($this->buildParcel('MXL0000000001'));

        $request = GetParcelLabelRequest::fromParcelResponse($response);

        $this->assertSame('MXL0000000001', $request->getIdentifier());
    }

    public function testFromParcelThrowsWhenLabelIsNull(): void
    {
        $parcel = $this->buildParcel(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('only available for B2C parcels');

        GetParcelLabelRequest::fromParcel($parcel);
    }

    public function testFromParcelResponseThrowsWhenLabelIsNull(): void
    {
        $response = new ParcelDetailResponse($this->buildParcel(null));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('only available for B2C parcels');

        GetParcelLabelRequest::fromParcelResponse($response);
    }

    private function buildParcel(?string $label): Parcel
    {
        $pudo = new PickUpDropOff(
            'PUDO_001',
            'MX001',
            'pudo',
            'PUDO Central',
            '',
            new Address('06600', 'CDMX', 'Calle 1 #123', new Coordinate(19.4326, -99.1332)),
            'Lun-Vie: 09:00-18:00',
            [],
            '+523334445556',
            true,
            Date::from('2023-01-01T00:00:00+00:00')
        );

        return new Parcel(
            'PARCEL_001',
            'MXT0000000001',
            'https://example.com/qr/MXT0000000001.png',
            $label,
            null,
            new ParcelContent('Libro', null, null, null),
            ParcelStatus::from('created'),
            [],
            new Person('Juan', 'Garcia', 'juan@example.com'),
            new Person('Ana', 'Lopez', 'ana@example.com'),
            null,
            $pudo,
            Date::from('2024-01-01T10:00:00+00:00'),
            null
        );
    }
}
