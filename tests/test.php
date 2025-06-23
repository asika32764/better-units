<?php

declare(strict_types=1);

use Asika\UnitConverter\Compound\Speed;
use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

$d = Duration::parse('1year');

show($d->format());

// $s = new Speed(1, 'km/h')
//     ->convertTo('m/s', scale: 10);
//
// show($s);
