<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Utils;

use InvalidArgumentException;

class Getter
{
    /**
     * When $field is null, $data must be a string and is returned. Otherwise $data must be an array and $data[$field] a string.
     *
     * @param mixed $data
     */
    public static function requireString($data, ?string $field, string $context): string
    {
        if ($field === null) {
            if (!is_string($data)) {
                throw new InvalidArgumentException(
                    sprintf("Missing or invalid value in %s (expected string)", $context)
                );
            }

            return $data;
        }

        if (!is_array($data) || !isset($data[$field]) || !is_string($data[$field])) {
            throw new InvalidArgumentException(
                sprintf("Missing or invalid '%s' field in %s (expected string)", $field, $context)
            );
        }

        return $data[$field];
    }

    /**
     * When $field is null, $data must be an array and is returned. Otherwise $data must be an array and $data[$field] an array.
     *
     * @param mixed $data
     *
     * @return array<mixed>
     */
    public static function requireArray($data, ?string $field, string $context): array
    {
        if ($field === null) {
            if (!is_array($data)) {
                throw new InvalidArgumentException(
                    sprintf("Missing or invalid value in %s (expected array)", $context)
                );
            }

            return $data;
        }

        if (!is_array($data) || !isset($data[$field]) || !is_array($data[$field])) {
            throw new InvalidArgumentException(
                sprintf("Missing or invalid '%s' field in %s (expected array)", $field, $context)
            );
        }

        return $data[$field];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function requireBool(array $data, string $field, string $context): bool
    {
        if (!isset($data[$field]) || !is_bool($data[$field])) {
            throw new InvalidArgumentException(
                sprintf("Missing or invalid '%s' field in %s (expected bool)", $field, $context)
            );
        }

        return $data[$field];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function requireFloat(array $data, string $field, string $context): float
    {
        if (!isset($data[$field]) || (!is_float($data[$field]) && !is_int($data[$field]))) {
            throw new InvalidArgumentException(
                sprintf("Missing or invalid '%s' field in %s (expected float)", $field, $context)
            );
        }

        return (float) $data[$field];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function requireInt(array $data, string $field, string $context): int
    {
        if (!isset($data[$field]) || !is_int($data[$field])) {
            throw new InvalidArgumentException(
                sprintf("Missing or invalid '%s' field in %s (expected int)", $field, $context)
            );
        }

        return $data[$field];
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function optionalString(array $data, string $field): ?string
    {
        return isset($data[$field]) && is_string($data[$field]) ? $data[$field] : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function optionalFloat(array $data, string $field): ?float
    {
        return isset($data[$field]) && (is_float($data[$field]) || is_int($data[$field]))
            ? (float) $data[$field]
            : null;
    }

    /**
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed>|null
     */
    public static function optionalArray(array $data, string $field): ?array
    {
        return isset($data[$field]) && is_array($data[$field]) ? $data[$field] : null;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function optionalInt(array $data, string $field): ?int
    {
        return isset($data[$field]) && is_int($data[$field]) ? $data[$field] : null;
    }
}
