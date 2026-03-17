<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request\DTO;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\PersonData;

class PersonDataTest extends TestCase
{
    public function testToArrayWithRequiredFieldsOnly(): void
    {
        $person = new PersonData('Juan', 'García', 'juan@example.com');

        $this->assertSame([
            'first_name' => 'Juan',
            'last_name' => 'García',
            'email' => 'juan@example.com',
        ], $person->toArray());
    }

    public function testToArrayWithAllFields(): void
    {
        $person = new PersonData('Juan', 'García', 'juan@example.com', '+525512345678', '06600');

        $this->assertSame([
            'first_name' => 'Juan',
            'last_name' => 'García',
            'email' => 'juan@example.com',
            'phone' => '+525512345678',
            'postal_code' => '06600',
        ], $person->toArray());
    }

    public function testToArrayOmitsNullPhone(): void
    {
        $person = new PersonData('Juan', 'García', 'juan@example.com', null, '06600');

        $result = $person->toArray();

        $this->assertArrayNotHasKey('phone', $result);
        $this->assertArrayHasKey('postal_code', $result);
    }

    public function testToArrayOmitsNullPostalCode(): void
    {
        $person = new PersonData('Juan', 'García', 'juan@example.com', '+525512345678', null);

        $result = $person->toArray();

        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayNotHasKey('postal_code', $result);
    }

    public function testToArrayOmitsBothNullOptionals(): void
    {
        $person = new PersonData('Juan', 'García', 'juan@example.com');

        $result = $person->toArray();

        $this->assertCount(3, $result);
        $this->assertArrayNotHasKey('phone', $result);
        $this->assertArrayNotHasKey('postal_code', $result);
    }

    public function testToArrayWithEmptyStrings(): void
    {
        $person = new PersonData('', '', '');

        $this->assertSame([
            'first_name' => '',
            'last_name' => '',
            'email' => '',
        ], $person->toArray());
    }

    public function testToArrayWithSpecialCharactersInName(): void
    {
        $person = new PersonData('José María', 'Ñoño-López', 'jose@example.mx');

        $result = $person->toArray();

        $this->assertSame('José María', $result['first_name']);
        $this->assertSame('Ñoño-López', $result['last_name']);
    }
}
