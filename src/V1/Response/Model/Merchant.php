<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class Merchant
{
    private string $id;
    private string $name;
    private bool $enabled;
    private bool $webhookEnabled;
    private ?string $webhookUrl;
    private string $createdAt;
    /** @var User[] */
    private array $users;
    /** @var PickUpDropOff[] */
    private array $pudos;

    /**
     * @param User[]           $users
     * @param PickUpDropOff[] $pudos
     */
    public function __construct(
        string $id,
        string $name,
        bool $enabled,
        bool $webhookEnabled,
        ?string $webhookUrl,
        string $createdAt,
        array $users,
        array $pudos
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->webhookEnabled = $webhookEnabled;
        $this->webhookUrl = $webhookUrl;
        $this->createdAt = $createdAt;
        $this->users = $users;
        $this->pudos = $pudos;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $usersRaw = Getter::requireArray($data, 'users', 'Merchant');
        $users = array_map(
            fn ($user, $index): User => User::fromArray(
                Getter::requireArray($user, null, sprintf('Merchant users[%s]', (string) $index))
            ),
            $usersRaw,
            array_keys($usersRaw)
        );

        $pudosRaw = Getter::requireArray($data, 'pudos', 'Merchant');
        $pudos = array_map(
            fn ($pudo, $index): PickUpDropOff => PickUpDropOff::fromArray(
                Getter::requireArray($pudo, null, sprintf('Merchant pudos[%s]', (string) $index))
            ),
            $pudosRaw,
            array_keys($pudosRaw)
        );

        return new self(
            Getter::requireString($data, 'id', 'Merchant'),
            Getter::requireString($data, 'name', 'Merchant'),
            Getter::requireBool($data, 'enabled', 'Merchant'),
            Getter::requireBool($data, 'webhook_enabled', 'Merchant'),
            Getter::optionalString($data, 'webhook_url'),
            Getter::requireString($data, 'created_at', 'Merchant'),
            $users,
            $pudos
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isWebhookEnabled(): bool
    {
        return $this->webhookEnabled;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhookUrl;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return PickUpDropOff[]
     */
    public function getPudos(): array
    {
        return $this->pudos;
    }
}
