<?php

declare(strict_types=1);

namespace Asika\BetterUnits;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

interface MeasurementInterface
{
    public BigDecimal $value {
        get;
    }

    public int $scale {
        get;
    }

    public string $unit {
        get;
    }

    public string $atomUnit {
        get;
    }

    public string $defaultUnit {
        get;
    }

    public string $baseUnit {
        get;
    }

    #[\NoDiscard]
    public function convertTo(
        string $toUnit,
        int|null|false $scale = null,
        RoundingMode $roundingMode = RoundingMode::Down
    ): static;

    public function to(string $unit, ?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down): BigDecimal;

    public function format(
        \Closure|string|null $suffix = null,
        ?string $unit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::Down
    ): string;
}
