<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

class ParcelLabelResponse
{
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
}
