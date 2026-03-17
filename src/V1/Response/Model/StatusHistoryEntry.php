<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use DateTimeImmutable;
use PuntoPost\Sdk\Utils\Date;
use PuntoPost\Sdk\Utils\Getter;
use PuntoPost\Sdk\V1\Response\Model\Enum\ParcelStatus;

class StatusHistoryEntry
{
    private ParcelStatus $status;
    private DateTimeImmutable $when;

    public function __construct(ParcelStatus $status, DateTimeImmutable $when)
    {
        $this->status = $status;
        $this->when = $when;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            ParcelStatus::from(Getter::requireString($data, 'status', 'StatusHistoryEntry')),
            Date::from(Getter::requireString($data, 'when', 'StatusHistoryEntry'))
        );
    }

    public function getStatus(): ParcelStatus
    {
        return $this->status;
    }

    public function getWhen(): DateTimeImmutable
    {
        return $this->when;
    }
}
