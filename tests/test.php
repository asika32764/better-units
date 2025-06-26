<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$d = Duration::from('350years');
$d = $d->withAddedUnitExchangeRate(
    'centuries',
    $d->yearSeconds->multipliedBy(100)
);
$d = $d->convertTo('centuries', 1);

$d->withUnitExchanges(
    [
        Duration::UNIT_FEMTOSECONDS => '1',
        Duration::UNIT_PICOSECONDS => '1000',
        Duration::UNIT_NANOSECONDS => '1000000',
        Duration::UNIT_MICROSECONDS => '1000000000',
        Duration::UNIT_MILLISECONDS => '1000000000000',
        Duration::UNIT_SECONDS => '1000000000000000',
        Duration::UNIT_MINUTES => '60000000000000',
        Duration::UNIT_HOURS => '3600000000000000',
        Duration::UNIT_DAYS => '86400000000000000',
        Duration::UNIT_WEEKS => '604800000000000000',
        Duration::UNIT_MONTHS => '2629800000000000000',
        Duration::UNIT_YEARS => '31557600000000000000',
    ],
    defaultUnit: Duration::UNIT_SECONDS
);
