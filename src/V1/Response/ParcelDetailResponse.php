<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\V1\Response\Model\Parcel;

class ParcelDetailResponse
{
    private Parcel $detail;

    public function __construct(Parcel $detail)
    {
        $this->detail = $detail;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Parcel::fromArray(isset($data['detail']) && is_array($data['detail']) ? $data['detail'] : [])
        );
    }

    public function getDetail(): Parcel
    {
        return $this->detail;
    }
}
