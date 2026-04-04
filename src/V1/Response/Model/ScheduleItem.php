<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\V1\Response\Model;

use PuntoPost\Sdk\Utils\Getter;

class ScheduleItem
{
    private string $day;
    private string $start;
    private string $end;

    public function __construct(string $day, string $start, string $end)
    {
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            Getter::requireString($data, 'day', 'ScheduleItem'),
            Getter::requireString($data, 'start', 'ScheduleItem'),
            Getter::requireString($data, 'end', 'ScheduleItem')
        );
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getEnd(): string
    {
        return $this->end;
    }
}
