<?php

declare(strict_types=1);

namespace PuntoPost\Sdk\Utils;

use DateTimeImmutable;
use DateTimeZone;

class Date
{
    public static function from(string $date): DateTimeImmutable
    {
        return new DateTimeImmutable($date, new DateTimeZone('America/Mexico_City'));
    }
}
