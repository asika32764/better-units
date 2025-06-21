<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

show(
    Duration::intervalToMicroseconds(
        DateInterval::createFromDateString('1years 10months 1weeks 6days 39minutes 14seconds 400milliseconds')
    )
);

$d = new Duration(16384, Duration::UNIT_HOURS)
    ->convertTo(Duration::UNIT_YEARS, 3)
    ->convertTo(Duration::UNIT_SECONDS);
show((string) $d->value);

