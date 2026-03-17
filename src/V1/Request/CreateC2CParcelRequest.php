<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request;

use PuntoPost\Sdk\V1\Request\DTO\ParcelContentData;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class CreateC2CParcelRequest
{
    private string $merchantId;
    private ParcelContentData $content;
    private PersonData $sender;
    private PersonData $receiver;
    private string $destinationId;

    public function __construct(
        string $merchantId,
        ParcelContentData $content,
        PersonData $sender,
        PersonData $receiver,
        string $destinationId
    ) {
        $this->merchantId = $merchantId;
        $this->content = $content;
        $this->sender = $sender;
        $this->receiver = $receiver;
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
            'receiver' => $this->receiver->toArray(),
            'destination_id' => $this->destinationId,
        ];
    }
}
