<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$s = \Asika\UnitConverter\Compound\Speed::from(100, 'km/h');

show(
    $s->measure->baseUnit,
    $s->deno->baseUnit,
);

$s = $s->convertTo('mps', scale: 4);
show($s->format());
