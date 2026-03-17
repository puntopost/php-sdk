<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;
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
            Getter::requireString($data, 'id', 'User'),
            Getter::requireString($data, 'username', 'User'),
            Getter::requireString($data, 'email', 'User'),
            UserType::from(Getter::requireString($data, 'type', 'User')),
            Getter::requireBool($data, 'enabled', 'User'),
            Date::from(Getter::requireString($data, 'created_at', 'User'))
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
