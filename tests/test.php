<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$duration = Duration::from('300seconds');
$duration = $duration->withUnitNormalizer(
    function () {
        //
    }
);
echo serialize($duration);
