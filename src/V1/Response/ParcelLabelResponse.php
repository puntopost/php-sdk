<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

use InvalidArgumentException;

class ParcelLabelResponse
{
    public const EXTENSION_PDF = 'pdf';
    public const EXTENSION_PNG = 'png';

    private const EXTENSIONS_BY_CONTENT_TYPE = [
        'application/pdf' => self::EXTENSION_PDF,
        'image/png' => self::EXTENSION_PNG,
    ];

    private string $content;
    private string $contentType;

    public function __construct(string $content, string $contentType)
    {
        $this->content = $content;
        $this->contentType = $contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getExtension(): string
    {
        if (!isset(self::EXTENSIONS_BY_CONTENT_TYPE[$this->contentType])) {
            throw new InvalidArgumentException(
                sprintf('Unsupported parcel label content type: %s', $this->contentType)
            );
        }

        return self::EXTENSIONS_BY_CONTENT_TYPE[$this->contentType];
    }
}
