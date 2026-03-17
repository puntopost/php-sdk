<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class CreateC2BParcelRequest
{
    private string $merchantId;
    private ParcelContentData $content;
    private PersonData $sender;
    private string $destinationId;

    public function __construct(
        string $merchantId,
        ParcelContentData $content,
        PersonData $sender,
        string $destinationId
    ) {
        $this->merchantId = $merchantId;
        $this->content = $content;
        $this->sender = $sender;
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
            'sender' => $this->sender->toArray(),
            'destination_id' => $this->destinationId,
        ];
    }
}
