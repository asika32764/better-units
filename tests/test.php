<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

echo new Duration()
    ->withAnomalisticCalendar()
    ->withParse('1years 1months 1weeks 6days 18hours 39minutes 16seconds')
    ->convertTo(Duration::UNIT_SECONDS)
    ->withExtract(Duration::UNIT_YEARS)[1]
    ->format(unit: Duration::UNIT_MONTHS, scale: 9);

