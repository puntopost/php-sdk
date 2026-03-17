<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Request\DTO;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Request\DTO\Pagination;

class PaginationTest extends TestCase
{
    public function testConstructorStoresValues(): void
    {
        $pagination = new Pagination(0, 10);

        $this->assertSame(0, $pagination->getOffset());
        $this->assertSame(10, $pagination->getLimit());
    }

    public function testToStringProducesLimitDashOffsetFormat(): void
    {
        $pagination = new Pagination(0, 10);

        $this->assertSame('10-0', (string) $pagination);
    }

    public function testToStringWithNonZeroOffset(): void
    {
        $pagination = new Pagination(5, 10);

        $this->assertSame('10-5', (string) $pagination);
    }

    public function testFromWithValidCursor(): void
    {
        $pagination = Pagination::from('10-5');

        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertSame(5, $pagination->getOffset());
        $this->assertSame(10, $pagination->getLimit());
    }

    public function testRoundTripIsConsistent(): void
    {
        $original = '10-5';
        $pagination = Pagination::from($original);

        $this->assertNotNull($pagination);
        $this->assertSame($original, (string) $pagination);
    }

    public function testFromWithNoDashReturnsNull(): void
    {
        $this->assertNull(Pagination::from('nodash'));
    }

    public function testFromWithEmptyStringReturnsNull(): void
    {
        $this->assertNull(Pagination::from(''));
    }

    public function testFromWithTooManyPartsReturnsNull(): void
    {
        $this->assertNull(Pagination::from('5-0-3'));
    }

    public function testFromWithNumericZeroZero(): void
    {
        $pagination = Pagination::from('0-0');

        $this->assertNotNull($pagination);
        $this->assertSame(0, $pagination->getLimit());
        $this->assertSame(0, $pagination->getOffset());
    }

    public function testFromWithNonNumericPartsProducesZeroCast(): void
    {
        $pagination = Pagination::from('abc-xyz');

        $this->assertNotNull($pagination);
        $this->assertSame(0, $pagination->getLimit());
        $this->assertSame(0, $pagination->getOffset());
    }

    public function testFromWithSingleDashReturnsZeroValues(): void
    {
        $pagination = Pagination::from('-');

        $this->assertNotNull($pagination);
        $this->assertSame(0, $pagination->getLimit());
        $this->assertSame(0, $pagination->getOffset());
    }
}
