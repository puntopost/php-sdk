<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Exception;

class ValidationException extends PuntoPostException
{
    /** @var array<string,string> */
    private array $fieldErrors;

    /**
     * @param array<string,string> $fieldErrors
     */
    private function __construct(
        int $statusCode,
        string $rawBody,
        string $errorType = '',
        string $errorTitle = '',
        string $errorDetail = '',
        string $errorInstance = '',
        array $fieldErrors = []
    ) {
        parent::__construct($statusCode, $rawBody, $errorType, $errorTitle, $errorDetail, $errorInstance);

        $this->fieldErrors = $fieldErrors;
    }

    /**
     * Creates an instance by parsing the raw JSON body.
     * The `errors` field is expected to be an associative object mapping field names to messages.
     */
    public static function fromResponse(int $statusCode, string $rawBody): self
    {
        $data = json_decode($rawBody, true);

        if (!is_array($data)) {
            return new self($statusCode, $rawBody);
        }

        $fieldErrors = [];
        if (isset($data['errors']) && is_array($data['errors'])) {
            /** @var array<int|string,mixed> $errors */
            $errors = $data['errors'];
            $keys = array_map(fn ($field): string => (string) $field, array_keys($errors));
            $values = array_values(array_map(
                fn ($messages): string => is_array($messages)
                    ? implode(', ', array_map(
                        fn ($m): string => is_scalar($m) || $m === null ? (string) $m : '',
                        $messages
                    ))
                    : (is_scalar($messages) || $messages === null ? (string) $messages : ''),
                $errors
            ));
            $combined = array_combine($keys, $values);
            $fieldErrors = is_array($combined) ? $combined : [];
        }

        return new self(
            $statusCode,
            $rawBody,
            (string) ($data['type'] ?? ''),
            (string) ($data['title'] ?? ''),
            (string) ($data['detail'] ?? ''),
            (string) ($data['instance'] ?? ''),
            $fieldErrors
        );
    }

    /**
     * Returns field-level validation errors as an associative array: field => message.
     *
     * @return array<string,string>
     */
    public function getFieldErrors(): array
    {
        return $this->fieldErrors;
    }
}
