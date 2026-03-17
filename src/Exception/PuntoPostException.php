<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Exception;

use RuntimeException;

class PuntoPostException extends RuntimeException
{
    private int $statusCode;
    private string $rawBody;
    private string $errorType;
    private string $errorTitle;
    private string $errorDetail;
    private string $errorInstance;

    protected function __construct(
        int $statusCode,
        string $rawBody,
        string $errorType = '',
        string $errorTitle = '',
        string $errorDetail = '',
        string $errorInstance = ''
    ) {
        $this->statusCode = $statusCode;
        $this->rawBody = $rawBody;
        $this->errorType = $errorType;
        $this->errorTitle = $errorTitle;
        $this->errorDetail = $errorDetail;
        $this->errorInstance = $errorInstance;

        if ($errorDetail !== '') {
            $message = $errorDetail;
        } elseif ($errorTitle !== '') {
            $message = $errorTitle;
        } else {
            $message = 'HTTP ' . $statusCode . ' error';
        }

        parent::__construct($message, $statusCode);
    }

    /**
     * Creates an instance by parsing the raw JSON body defensively.
     * If the body is not valid JSON or fields are missing, they default to empty string.
     */
    public static function fromResponse(int $statusCode, string $rawBody): self
    {
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            return new self($statusCode, $rawBody);
        }

        return new self(
            $statusCode,
            $rawBody,
            isset($data['type']) ? (string) $data['type'] : '',
            isset($data['title']) ? (string) $data['title'] : '',
            isset($data['detail']) ? (string) $data['detail'] : '',
            isset($data['instance']) ? (string) $data['instance'] : ''
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function getErrorTitle(): string
    {
        return $this->errorTitle;
    }

    public function getErrorDetail(): string
    {
        return $this->errorDetail;
    }

    public function getErrorInstance(): string
    {
        return $this->errorInstance;
    }
}
