<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class CreateB2CParcelRequest
{
    private string $merchantId;
    private ParcelContentData $content;
    private PersonData $receiver;
    private string $originId;
    private string $destinationId;

    public function __construct(
        string $merchantId,
        ParcelContentData $content,
        PersonData $receiver,
        string $originId,
        string $destinationId
    ) {
        $this->merchantId = $merchantId;
        $this->content = $content;
        $this->receiver = $receiver;
        $this->originId = $originId;
        $this->destinationId = $destinationId;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content->toArray(),
            'receiver' => $this->receiver->toArray(),
            'origin_id' => $this->originId,
            'destination_id' => $this->destinationId,
        ];
    }
}
