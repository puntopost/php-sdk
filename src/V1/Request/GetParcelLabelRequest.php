<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use InvalidArgumentException;
use PuntoPost\Sdk\V1\Response\Model\Parcel;
use PuntoPost\Sdk\V1\Response\ParcelDetailResponse;

final class GetParcelLabelRequest
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
        $label = $parcel->getLabel();
        if ($label === null) {
            throw new InvalidArgumentException(
                'Cannot download label for parcel without a label identifier (only available for B2C parcels).'
            );
        }

        return new self($label);
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
