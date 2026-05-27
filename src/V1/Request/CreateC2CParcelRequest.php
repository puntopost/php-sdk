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
    private ?string $merchantReference;

    public function __construct(
        string $merchantId,
        ParcelContentData $content,
        PersonData $sender,
        PersonData $receiver,
        string $destinationId,
        ?string $merchantReference = null
    ) {
        $this->merchantId = $merchantId;
        $this->content = $content;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->destinationId = $destinationId;
        $this->merchantReference = $merchantReference;
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
        $payload = [
            'content' => $this->content->toArray(),
            'sender' => $this->sender->toArray(),
            'receiver' => $this->receiver->toArray(),
            'destination_id' => $this->destinationId,
        ];

        if ($this->merchantReference !== null) {
            $payload['merchant_reference'] = $this->merchantReference;
        }

        return $payload;
    }
}
