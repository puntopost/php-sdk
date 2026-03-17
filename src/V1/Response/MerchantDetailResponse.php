<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use PuntoPost\Sdk\Utils\Getter;
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
            Merchant::fromArray(Getter::requireArray($data, 'detail', 'MerchantDetailResponse'))
        );
    }

    public function getDetail(): Merchant
    {
        return $this->detail;
    }
}
