<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

class Person
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private ?string $phone;
    private ?string $postalCode;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone = null,
        ?string $postalCode = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->postalCode = $postalCode;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['first_name']) && is_string($data['first_name']) ? $data['first_name'] : '',
            isset($data['last_name']) && is_string($data['last_name']) ? $data['last_name'] : '',
            isset($data['email']) && is_string($data['email']) ? $data['email'] : '',
            isset($data['phone']) && is_string($data['phone']) ? $data['phone'] : null,
            isset($data['postal_code']) && is_string($data['postal_code']) ? $data['postal_code'] : null
        );
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
}
