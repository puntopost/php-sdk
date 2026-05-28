<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\V1\Response\Model\Parcel;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;

final class GetParcelTrackingQrRequest
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function fromParcelResponse(ParcelDetailResponse $response): self
    {
        return self::fromParcel($response->getDetail());
    }

    public static function fromParcel(Parcel $parcel): self
    {
        return new self($parcel->getTracking());
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
