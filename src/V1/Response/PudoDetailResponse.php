<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\V1\Response\Model\PickUpDropOff;

class PudoDetailResponse
{
    private PickUpDropOff $detail;

    public function __construct(PickUpDropOff $detail)
    {
        $this->detail = $detail;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            PickUpDropOff::fromArray(isset($data['detail']) && is_array($data['detail']) ? $data['detail'] : [])
        );
    }

    public function getDetail(): PickUpDropOff
    {
        return $this->detail;
    }
}
