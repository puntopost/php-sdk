<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Person;

class PersonTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $person = new Person('Juan', 'García', 'juan@example.com', '+5255', '06600');

        $this->assertSame('Juan', $person->getFirstName());
        $this->assertSame('García', $person->getLastName());
        $this->assertSame('juan@example.com', $person->getEmail());
        $this->assertSame('+5255', $person->getPhone());
        $this->assertSame('06600', $person->getPostalCode());
    }

    public function testConstructorWithNullOptionals(): void
    {
        $person = new Person('Juan', 'García', 'juan@example.com');

        $this->assertNull($person->getPhone());
        $this->assertNull($person->getPostalCode());
    }

    public function testFromArrayWithFullPayload(): void
    {
        $person = Person::fromArray([
            'first_name' => 'Juan',
            'last_name' => 'García',
            'email' => 'juan@example.com',
            'phone' => '+5255',
            'postal_code' => '06600',
        ]);

        $this->assertSame('Juan', $person->getFirstName());
        $this->assertSame('García', $person->getLastName());
        $this->assertSame('juan@example.com', $person->getEmail());
        $this->assertSame('+5255', $person->getPhone());
        $this->assertSame('06600', $person->getPostalCode());
    }

    public function testFromArrayWithoutOptionalFields(): void
    {
        $person = Person::fromArray([
            'first_name' => 'Juan',
            'last_name' => 'García',
            'email' => 'juan@example.com',
        ]);

        $this->assertNull($person->getPhone());
        $this->assertNull($person->getPostalCode());
    }

    public function testFromArrayWithEmptyArrayDefaultsToEmptyStrings(): void
    {
        $person = Person::fromArray([]);

        $this->assertSame('', $person->getFirstName());
        $this->assertSame('', $person->getLastName());
        $this->assertSame('', $person->getEmail());
        $this->assertNull($person->getPhone());
        $this->assertNull($person->getPostalCode());
    }

    public function testFromArrayWithWrongTypesDefaultToEmptyStrings(): void
    {
        $person = Person::fromArray([
            'first_name' => 42,
            'last_name' => ['García'],
            'email' => true,
            'phone' => 5255,
            'postal_code' => 6600,
        ]);

        $this->assertSame('', $person->getFirstName());
        $this->assertSame('', $person->getLastName());
        $this->assertSame('', $person->getEmail());
        $this->assertNull($person->getPhone());
        $this->assertNull($person->getPostalCode());
    }

    public function testFromArrayWithGarbagePayload(): void
    {
        $person = Person::fromArray(['not_first' => 'foo', 'not_last' => 'bar']);

        $this->assertSame('', $person->getFirstName());
        $this->assertSame('', $person->getLastName());
        $this->assertSame('', $person->getEmail());
    }
}
