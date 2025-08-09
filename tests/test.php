<?php

declare(strict_types=1);

use Asika\BetterUnits\Compound\Speed;
use Asika\BetterUnits\Duration;

include __DIR__ . '/../vendor/autoload.php';

$mps = new Speed()
    ->withIntermediateScale(20)
    ->withParse('1kph')
    ->toMps(scale: 10);

show((string) $mps);

$mps = new Speed()
    ->withIntermediateScale(99)
    ->withParse('1kph')
    ->toMps(scale: 10);

show((string) $mps);
