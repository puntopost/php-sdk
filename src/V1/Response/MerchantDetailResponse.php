<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\V1\Response\Model\Merchant;

class MerchantDetailResponse
{
    private Merchant $detail;

    public function __construct(Merchant $detail)
    {
        $this->detail = $detail;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Merchant::fromArray(isset($data['detail']) && is_array($data['detail']) ? $data['detail'] : [])
        );
    }

    public function getDetail(): Merchant
    {
        return $this->detail;
    }
}
