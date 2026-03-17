<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model;

use InvalidArgumentException;
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

    public function testFromArrayWithEmptyArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Person::fromArray([]);
    }

    public function testFromArrayWithWrongTypesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Person::fromArray([
            'first_name' => 42,
            'last_name' => ['García'],
            'email' => true,
            'phone' => 5255,
            'postal_code' => 6600,
        ]);
    }

    public function testFromArrayWithGarbagePayloadThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Person::fromArray(['not_first' => 'foo', 'not_last' => 'bar']);
    }
}
