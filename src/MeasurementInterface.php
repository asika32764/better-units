<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;

interface MeasurementInterface
{
    public BigDecimal $value {
        get;
    }

    public string $unit {
        get;
    }
}
