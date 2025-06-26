<?php

declare(strict_types=1);

use Asika\UnitConverter\Compound\Speed;
use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$duration = new Duration(1000500, 's');
echo $duration->serialize(
    [
        Duration::UNIT_FEMTOSECONDS,
    ]
);
