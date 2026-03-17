<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

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
    /** @var MerchantPickUpDropOff[] */
    private array $pudos;

    /**
     * @param User[]                  $users
     * @param MerchantPickUpDropOff[] $pudos
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
        $users = [];
        if (isset($data['users']) && is_array($data['users'])) {
            foreach ($data['users'] as $user) {
                if (is_array($user)) {
                    $users[] = User::fromArray($user);
                }
            }
        }

        $pudos = [];
        if (isset($data['pudos']) && is_array($data['pudos'])) {
            foreach ($data['pudos'] as $pudo) {
                if (is_array($pudo)) {
                    $pudos[] = MerchantPickUpDropOff::fromArray($pudo);
                }
            }
        }

        return new self(
            isset($data['id']) && is_string($data['id']) ? $data['id'] : '',
            isset($data['name']) && is_string($data['name']) ? $data['name'] : '',
            isset($data['enabled']) && is_bool($data['enabled']) && $data['enabled'],
            isset($data['webhook_enabled']) && is_bool($data['webhook_enabled']) && $data['webhook_enabled'],
            isset($data['webhook_url']) && is_string($data['webhook_url']) ? $data['webhook_url'] : null,
            isset($data['created_at']) && is_string($data['created_at']) ? $data['created_at'] : '',
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
     * @return MerchantPickUpDropOff[]
     */
    public function getPudos(): array
    {
        return $this->pudos;
    }
}
