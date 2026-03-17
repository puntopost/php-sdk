<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Request\DTO;

class PersonData
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
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
        ];

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }
        if ($this->postalCode !== null) {
            $data['postal_code'] = $this->postalCode;
        }

        return $data;
    }
}
