<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\V1\Response\Model\Enum\UserType;

class User
{
    private string $id;
    private string $username;
    private string $email;
    private UserType $type;
    private bool $enabled;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        string $username,
        string $email,
        UserType $type,
        bool $enabled,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->type = $type;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['id']) && is_string($data['id']) ? $data['id'] : '',
            isset($data['username']) && is_string($data['username']) ? $data['username'] : '',
            isset($data['email']) && is_string($data['email']) ? $data['email'] : '',
            UserType::from(isset($data['type']) && is_string($data['type']) ? $data['type'] : ''),
            isset($data['enabled']) && is_bool($data['enabled']) && $data['enabled'],
            Date::from(isset($data['created_at']) && is_string($data['created_at']) ? $data['created_at'] : '')
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getType(): UserType
    {
        return $this->type;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
