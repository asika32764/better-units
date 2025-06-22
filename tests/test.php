<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$s = \Asika\UnitConverter\Compound\Speed::from(100, 'km/h');

$s = $s->convertTo('m/s', scale: 4);
show(
    $s->measure->baseUnit,
    $s->deno->baseUnit,
);
show($s->format());
