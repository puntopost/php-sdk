<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response;

class LoginResponse
{
    private string $token;
    private int $expiresIn;

    public function __construct(string $token, int $expiresIn)
    {
        $this->token = $token;
        $this->expiresIn = $expiresIn;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['token']) && is_string($data['token']) ? $data['token'] : '',
            isset($data['expires_in']) && is_int($data['expires_in']) ? $data['expires_in'] : 0
        );
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }
}
