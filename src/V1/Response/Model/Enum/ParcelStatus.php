<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model\Enum;

class ParcelStatus
{
    public const CREATED = 'created';
    public const IN_ORIGIN_POINT = 'in_origin_point';
    public const IN_TRANSIT_DEPOT = 'in_transit_depot';
    public const IN_DEPOT = 'in_depot';
    public const IN_TRANSIT_DESTINATION = 'in_transit_destination';
    public const IN_DESTINATION_POINT = 'in_destination_point';
    public const IN_REROUTED_POINT = 'in_rerouted_point';
    public const DELIVERED = 'delivered';
    public const RETURN_IN_DESTINATION_POINT = 'return_in_destination_point';
    public const RETURN_IN_TRANSIT_DEPOT = 'return_in_transit_depot';
    public const RETURN_IN_DEPOT = 'return_in_depot';
    public const RETURN_IN_TRANSIT_ORIGIN = 'return_in_transit_origin';
    public const RETURN_IN_ORIGIN_POINT = 'return_in_origin_point';
    public const RETURN_IN_REROUTED_POINT = 'return_in_rerouted_point';
    public const RETURN_DELIVERED = 'return_delivered';
    public const RETURN_FAIL_IN_ORIGIN_POINT = 'return_fail_in_origin_point';
    public const RETURN_FAIL_IN_TRANSIT_DEPOT = 'return_fail_in_transit_depot';
    public const RETURN_FAIL_IN_DEPOT = 'return_fail_in_depot';
    public const RETURN_FAIL_DELIVERED = 'return_fail_delivered';
    public const INCIDENCE = 'incidence';
    public const CANCELLED = 'cancelled';
    public const LOST = 'lost';

    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isCreated(): bool
    {
        return $this->value === self::CREATED;
    }

    public function isInOriginPoint(): bool
    {
        return $this->value === self::IN_ORIGIN_POINT;
    }

    public function isTransitDepot(): bool
    {
        return $this->value === self::IN_TRANSIT_DEPOT;
    }

    public function isDepot(): bool
    {
        return $this->value === self::IN_DEPOT;
    }

    public function isTransitDestinationPoint(): bool
    {
        return $this->value === self::IN_TRANSIT_DESTINATION;
    }

    public function isDestinationPoint(): bool
    {
        return $this->value === self::IN_DESTINATION_POINT;
    }

    public function isReroutedPoint(): bool
    {
        return $this->value === self::IN_REROUTED_POINT;
    }

    public function isDelivered(): bool
    {
        return $this->value === self::DELIVERED;
    }

    public function isInReroutedPoint(): bool
    {
        return $this->value === self::IN_REROUTED_POINT;
    }

    public function isReturnInDestinationPoint(): bool
    {
        return $this->value === self::RETURN_IN_DESTINATION_POINT;
    }

    public function isReturnInTransitDepot(): bool
    {
        return $this->value === self::RETURN_IN_TRANSIT_DEPOT;
    }

    public function isReturnIndDepot(): bool
    {
        return $this->value === self::RETURN_IN_DEPOT;
    }

    public function isReturnInTransitOrigin(): bool
    {
        return $this->value === self::RETURN_IN_TRANSIT_ORIGIN;
    }

    public function isReturnInOriginPoint(): bool
    {
        return $this->value === self::RETURN_IN_ORIGIN_POINT;
    }

    public function isReturnInReroutedPoint(): bool
    {
        return $this->value === self::RETURN_IN_REROUTED_POINT;
    }

    public function isReturnDelivered(): bool
    {
        return $this->value === self::RETURN_DELIVERED;
    }

    public function isReturnFailInOriginPoint(): bool
    {
        return $this->value === self::RETURN_FAIL_IN_ORIGIN_POINT;
    }

    public function isReturnFailInTransitDepot(): bool
    {
        return $this->value === self::RETURN_FAIL_IN_TRANSIT_DEPOT;
    }

    public function isReturnFailInDepot(): bool
    {
        return $this->value === self::RETURN_FAIL_IN_DEPOT;
    }

    public function isReturnFailDelivered(): bool
    {
        return $this->value === self::RETURN_FAIL_DELIVERED;
    }

    public function isIncidence(): bool
    {
        return $this->value === self::INCIDENCE;
    }

    public function isCancelled(): bool
    {
        return $this->value === self::CANCELLED;
    }

    public function isLost(): bool
    {
        return $this->value === self::LOST;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }
}
