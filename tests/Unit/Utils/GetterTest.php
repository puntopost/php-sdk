<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\Utils;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\Utils\Getter;

class GetterTest extends TestCase
{
    public function testRequireStringReturnsValue(): void
    {
        $data = ['name' => 'John'];

        self::assertSame('John', Getter::requireString($data, 'name', 'test'));
    }

    public function testRequireStringThrowsOnMissingField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing or invalid 'name' field in test (expected string)");

        Getter::requireString([], 'name', 'test');
    }

    public function testRequireStringThrowsOnWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'age'");

        Getter::requireString(['age' => 123], 'age', 'test');
    }

    public function testRequireArrayReturnsValue(): void
    {
        $data = ['items' => ['a', 'b']];

        self::assertSame(['a', 'b'], Getter::requireArray($data, 'items', 'test'));
    }

    public function testRequireArrayThrowsOnMissingField(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing or invalid 'items' field in test (expected array)");

        Getter::requireArray([], 'items', 'test');
    }

    public function testRequireArrayThrowsOnWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'items'");

        Getter::requireArray(['items' => 'not_an_array'], 'items', 'test');
    }

    public function testRequireArrayNullFieldReturnsSameArray(): void
    {
        $a = ['x' => 1];
        self::assertSame($a, Getter::requireArray($a, null, 'ctx'));
        self::assertSame([], Getter::requireArray([], null, 'ctx'));
    }

    public function testRequireArrayNullFieldThrowsWhenNotArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid value in list[0] (expected array)');

        Getter::requireArray('not_array', null, 'list[0]');
    }

    public function testRequireStringNullFieldReturnsSameString(): void
    {
        self::assertSame('ab', Getter::requireString('ab', null, 'ctx'));
    }

    public function testRequireStringNullFieldThrowsWhenNotString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or invalid value in items[2] (expected string)');

        Getter::requireString(99, null, 'items[2]');
    }

    public function testOptionalStringReturnsValue(): void
    {
        self::assertSame('x', Getter::optionalString(['k' => 'x'], 'k'));
    }

    public function testOptionalStringReturnsNullWhenMissing(): void
    {
        self::assertNull(Getter::optionalString([], 'k'));
    }

    public function testOptionalStringReturnsNullWhenWrongType(): void
    {
        self::assertNull(Getter::optionalString(['k' => 1], 'k'));
    }

    public function testOptionalFloatReturnsFloatFromFloat(): void
    {
        self::assertSame(1.5, Getter::optionalFloat(['k' => 1.5], 'k'));
    }

    public function testOptionalFloatReturnsFloatFromInt(): void
    {
        self::assertSame(3.0, Getter::optionalFloat(['k' => 3], 'k'));
    }

    public function testOptionalFloatReturnsNullWhenMissing(): void
    {
        self::assertNull(Getter::optionalFloat([], 'k'));
    }

    public function testOptionalFloatReturnsNullWhenWrongType(): void
    {
        self::assertNull(Getter::optionalFloat(['k' => '1.0'], 'k'));
    }

    public function testOptionalArrayReturnsValue(): void
    {
        self::assertSame(['a' => 1], Getter::optionalArray(['k' => ['a' => 1]], 'k'));
    }

    public function testOptionalArrayReturnsNullWhenMissing(): void
    {
        self::assertNull(Getter::optionalArray([], 'k'));
    }

    public function testOptionalArrayReturnsNullWhenWrongType(): void
    {
        self::assertNull(Getter::optionalArray(['k' => 'not_array'], 'k'));
    }

    public function testOptionalIntReturnsValue(): void
    {
        self::assertSame(42, Getter::optionalInt(['k' => 42], 'k'));
    }

    public function testOptionalIntReturnsNullWhenMissing(): void
    {
        self::assertNull(Getter::optionalInt([], 'k'));
    }

    public function testOptionalIntReturnsNullWhenWrongType(): void
    {
        self::assertNull(Getter::optionalInt(['k' => '42'], 'k'));
        self::assertNull(Getter::optionalInt(['k' => 42.0], 'k'));
    }
}
