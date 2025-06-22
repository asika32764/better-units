<?php

declare(strict_types=1);

use Asika\UnitConverter\Duration;

include __DIR__ . '/../vendor/autoload.php';

echo \Brick\Math\BigDecimal::of(1)
    ->multipliedBy(1)
    ->dividedBy(3600, 10, \Brick\Math\RoundingMode::DOWN);
