<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$area = \Asika\UnitConverter\Area::from(401074580, 'm2')
    ->withOnlyCommonAreas();
echo $area->humanize(); // 401km2 74580m2
