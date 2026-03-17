<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Address;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;
use PuntoPost\Sdk\V1\Response\Model\Parcel;
use PuntoPost\Sdk\V1\Response\Model\ParcelContent;
use PuntoPost\Sdk\V1\Response\Model\Person;
use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;
use PuntoPost\Sdk\V1\Response\Model\StatusHistoryEntry;

class ParcelTest extends TestCase
{
    /**
     * @return array<string,mixed>
     */
    private function buildMinimalParcelData(): array
    {
        return [
            'id' => 'PCL-001',
            'tracking' => 'TRK-001',
            'qr_tracking' => 'QR-TRK-001',
            'content' => ['description' => 'Ropa', 'weight_kg' => 1.0],
            'status' => 'created',
            'status_history' => [],
            'sender' => ['first_name' => 'Juan', 'last_name' => 'García', 'email' => 'j@e.com'],
            'receiver' => ['first_name' => 'Ana', 'last_name' => 'López', 'email' => 'a@e.com'],
            'destination' => ['id' => 'DEST', 'external_id' => 'ED1', 'type' => 'pudo', 'name' => 'PUDO Dest',
                'description' => '', 'address' => ['postal_code' => '06600', 'city' => 'CDMX', 'address' => 'Calle 2', 'coordinate' => ['latitude' => 19.4326, 'longitude' => -99.1332]],
                'schedule' => 'Lun-Vie', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'],
            'created_at' => '2024-01-15T10:00:00+00:00',
        ];
    }

    public function testFromArrayWithMinimalPayload(): void
    {
        $parcel = Parcel::fromArray($this->buildMinimalParcelData());

        $this->assertSame('PCL-001', $parcel->getId());
        $this->assertSame('TRK-001', $parcel->getTracking());
        $this->assertSame('QR-TRK-001', $parcel->getQrTracking());
        $this->assertNull($parcel->getLabel());
        $this->assertNull($parcel->getQrLabel());
        $this->assertSame('Ropa', $parcel->getContent()->getDescription());
        $this->assertSame('created', $parcel->getStatus()->getValue());
        $this->assertCount(0, $parcel->getStatusHistory());
        $this->assertSame('Juan', $parcel->getSender()->getFirstName());
        $this->assertSame('Ana', $parcel->getReceiver()->getFirstName());
        $this->assertNull($parcel->getOrigin());
        $this->assertSame('DEST', $parcel->getDestination()->getId());
        $this->assertSame('2024-01-15', $parcel->getCreatedAt()->format('Y-m-d'));
        $this->assertNull($parcel->getExpireAt());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $data = $this->buildMinimalParcelData();
        $data['label'] = 'https://example.com/label.pdf';
        $data['qr_label'] = 'https://example.com/qr.pdf';
        $data['status_history'] = [
            ['status' => 'created', 'when' => '2024-01-15T10:00:00+00:00'],
            ['status' => 'in_origin_point', 'when' => '2024-01-15T12:00:00+00:00'],
        ];
        $data['origin'] = ['id' => 'ORIG', 'external_id' => 'EO1', 'type' => 'pudo', 'name' => 'PUDO Orig',
            'description' => '', 'address' => ['postal_code' => '64000', 'city' => 'MTY', 'address' => 'Calle 1', 'coordinate' => ['latitude' => 25.67, 'longitude' => -100.32]],
            'schedule' => 'Lun-Vie', 'enabled' => true, 'created_at' => '2024-01-01T00:00:00+00:00'];
        $data['expire_at'] = '2024-02-15T10:00:00+00:00';

        $parcel = Parcel::fromArray($data);

        $this->assertSame('https://example.com/label.pdf', $parcel->getLabel());
        $this->assertSame('https://example.com/qr.pdf', $parcel->getQrLabel());
        $this->assertCount(2, $parcel->getStatusHistory());
        $this->assertSame('created', $parcel->getStatusHistory()[0]->getStatus()->getValue());
        $this->assertSame('in_origin_point', $parcel->getStatusHistory()[1]->getStatus()->getValue());
        $this->assertNotNull($parcel->getOrigin());
        $this->assertSame('ORIG', $parcel->getOrigin()->getId());
        $expireAt = $parcel->getExpireAt();
        $this->assertNotNull($expireAt);
        $this->assertSame('2024-02-15', $expireAt->format('Y-m-d'));
    }

    public function testFromArrayOriginIsNullWhenAbsent(): void
    {
        $parcel = Parcel::fromArray($this->buildMinimalParcelData());

        $this->assertNull($parcel->getOrigin());
    }

    public function testFromArrayOriginIsNullWhenNotArray(): void
    {
        $data = $this->buildMinimalParcelData();
        $data['origin'] = 'not_an_array';

        $parcel = Parcel::fromArray($data);

        $this->assertNull($parcel->getOrigin());
    }

    public function testFromArrayExpireAtIsNullWhenAbsent(): void
    {
        $parcel = Parcel::fromArray($this->buildMinimalParcelData());

        $this->assertNull($parcel->getExpireAt());
    }

    public function testFromArrayExpireAtIsNullWhenNotString(): void
    {
        $data = $this->buildMinimalParcelData();
        $data['expire_at'] = ['2024-02-15'];

        $parcel = Parcel::fromArray($data);

        $this->assertNull($parcel->getExpireAt());
    }

    public function testFromArrayNonArrayStatusHistoryEntryThrows(): void
    {
        $data = $this->buildMinimalParcelData();
        $data['status_history'] = [
            ['status' => 'created', 'when' => '2024-01-15T10:00:00+00:00'],
            'not_an_array',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid value in Parcel status_history[1] (expected array)');

        Parcel::fromArray($data);
    }

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parcel::fromArray([]);
    }

    public function testFromArrayWithWrongTypesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parcel::fromArray([
            'id' => 99,
            'tracking' => null,
            'qr_tracking' => ['QR'],
            'label' => 123,
            'qr_label' => false,
            'status' => 42,
            'status_history' => 'not_an_array',
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Parcel::fromArray(['alpha' => 1, 'beta' => 'two', 'gamma' => [3], 'http_method' => 'DELETE']);
    }
}
