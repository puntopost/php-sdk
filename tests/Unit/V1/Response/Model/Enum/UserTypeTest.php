<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Tests\Unit\V1\Response\Model\Enum;

use PHPUnit\Framework\TestCase;
use PuntoPost\Sdk\V1\Response\Model\Enum\UserType;

class UserTypeTest extends TestCase
{
    public function testFromKnownValueStoresValue(): void
    {
        $type = UserType::from(UserType::MERCHANT);

        $this->assertSame('merchant', $type->getValue());
    }

    public function testFromUnknownValueIsAccepted(): void
    {
        $type = UserType::from('super_admin');

        $this->assertSame('super_admin', $type->getValue());
    }

    public function testFromEmptyStringIsAccepted(): void
    {
        $type = UserType::from('');

        $this->assertSame('', $type->getValue());
    }

    public function testIsStaff(): void
    {
        $this->assertTrue(UserType::from(UserType::STAFF)->isStaff());
        $this->assertFalse(UserType::from(UserType::MERCHANT)->isStaff());
    }

    public function testIsMerchant(): void
    {
        $this->assertTrue(UserType::from(UserType::MERCHANT)->isMerchant());
        $this->assertFalse(UserType::from(UserType::STAFF)->isMerchant());
    }

    public function testIsPudos(): void
    {
        $this->assertTrue(UserType::from(UserType::PUDOS)->isPudos());
        $this->assertFalse(UserType::from(UserType::STAFF)->isPudos());
    }

    public function testIsOperator(): void
    {
        $this->assertTrue(UserType::from(UserType::OPERATOR)->isOperator());
        $this->assertFalse(UserType::from(UserType::STAFF)->isOperator());
    }

    public function testIsControlTower(): void
    {
        $this->assertTrue(UserType::from(UserType::CONTROL_TOWER)->isControlTower());
        $this->assertFalse(UserType::from(UserType::STAFF)->isControlTower());
    }

    public function testUnknownValueReturnsFalseForAllHelpers(): void
    {
        $type = UserType::from('unknown_role');

        $this->assertFalse($type->isStaff());
        $this->assertFalse($type->isMerchant());
        $this->assertFalse($type->isPudos());
        $this->assertFalse($type->isOperator());
        $this->assertFalse($type->isControlTower());
    }
}
