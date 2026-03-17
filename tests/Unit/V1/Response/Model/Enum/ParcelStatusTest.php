<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model\Enum;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

class ParcelStatusTest extends TestCase
{
    public function testFromKnownValueStoresValue(): void
    {
        $status = ParcelStatus::from(ParcelStatus::CREATED);

        $this->assertSame('created', $status->getValue());
    }

    public function testFromUnknownValueIsAccepted(): void
    {
        $status = ParcelStatus::from('future_unknown_status');

        $this->assertSame('future_unknown_status', $status->getValue());
    }

    public function testFromEmptyStringIsAccepted(): void
    {
        $status = ParcelStatus::from('');

        $this->assertSame('', $status->getValue());
    }

    public function testIsCreated(): void
    {
        $this->assertTrue(ParcelStatus::from(ParcelStatus::CREATED)->isCreated());
        $this->assertFalse(ParcelStatus::from(ParcelStatus::DELIVERED)->isCreated());
    }

    public function testIsDelivered(): void
    {
        $this->assertTrue(ParcelStatus::from(ParcelStatus::DELIVERED)->isDelivered());
        $this->assertFalse(ParcelStatus::from(ParcelStatus::CREATED)->isDelivered());
    }

    public function testIsCancelled(): void
    {
        $this->assertTrue(ParcelStatus::from(ParcelStatus::CANCELLED)->isCancelled());
        $this->assertFalse(ParcelStatus::from(ParcelStatus::CREATED)->isCancelled());
    }

    public function testIsLost(): void
    {
        $this->assertTrue(ParcelStatus::from(ParcelStatus::LOST)->isLost());
        $this->assertFalse(ParcelStatus::from(ParcelStatus::CREATED)->isLost());
    }

    public function testIsIncidence(): void
    {
        $this->assertTrue(ParcelStatus::from(ParcelStatus::INCIDENCE)->isIncidence());
        $this->assertFalse(ParcelStatus::from(ParcelStatus::CREATED)->isIncidence());
    }

    public function testOnlyMatchingHelperReturnsTrue(): void
    {
        $status = ParcelStatus::from(ParcelStatus::IN_DEPOT);

        $this->assertTrue($status->isDepot());
        $this->assertFalse($status->isCreated());
        $this->assertFalse($status->isDelivered());
        $this->assertFalse($status->isCancelled());
    }

    public function testUnknownValueReturnsFalseForAllHelpers(): void
    {
        $status = ParcelStatus::from('not_a_real_status');

        $this->assertFalse($status->isCreated());
        $this->assertFalse($status->isDelivered());
        $this->assertFalse($status->isCancelled());
        $this->assertFalse($status->isLost());
        $this->assertFalse($status->isIncidence());
    }

    public function testAllKnownConstantsAreStrings(): void
    {
        $constants = [
            ParcelStatus::CREATED, ParcelStatus::IN_ORIGIN_POINT, ParcelStatus::IN_TRANSIT_DEPOT,
            ParcelStatus::IN_DEPOT, ParcelStatus::IN_TRANSIT_DESTINATION, ParcelStatus::IN_DESTINATION_POINT,
            ParcelStatus::IN_REROUTED_POINT, ParcelStatus::DELIVERED, ParcelStatus::RETURN_IN_DESTINATION_POINT,
            ParcelStatus::RETURN_IN_TRANSIT_DEPOT, ParcelStatus::RETURN_IN_DEPOT,
            ParcelStatus::RETURN_IN_TRANSIT_ORIGIN, ParcelStatus::RETURN_IN_ORIGIN_POINT,
            ParcelStatus::RETURN_IN_REROUTED_POINT, ParcelStatus::RETURN_DELIVERED,
            ParcelStatus::RETURN_FAIL_IN_ORIGIN_POINT, ParcelStatus::RETURN_FAIL_IN_TRANSIT_DEPOT,
            ParcelStatus::RETURN_FAIL_IN_DEPOT, ParcelStatus::RETURN_FAIL_DELIVERED,
            ParcelStatus::INCIDENCE, ParcelStatus::CANCELLED, ParcelStatus::LOST,
        ];

        foreach ($constants as $constant) {
            $this->assertIsString($constant);
        }
    }
}
