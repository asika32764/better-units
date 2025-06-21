<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Asika\UnitConverter\Concerns\DurationCalendlyTrait;
use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;

class ConvertHelper
{
    public static function dateIntervalToSeconds(
        \DateInterval $interval,
        BigNumber|int|float|string $yearSeconds = Duration::YEAR_SECONDS_COMMON,
        BigNumber|int|float|string $monthSeconds = Duration::MONTH_SECONDS_COMMON,
    ): BigDecimal {
        return BigDecimal::of($interval->y)->multipliedBy($yearSeconds)
            ->plus(BigDecimal::of($interval->m)->multipliedBy($monthSeconds))
            ->plus(BigDecimal::of($interval->d)->multipliedBy('86400'))
            ->plus(BigDecimal::of($interval->h)->multipliedBy('3600'))
            ->plus(BigDecimal::of($interval->i)->multipliedBy('60'))
            ->plus(BigDecimal::of($interval->s));
    }

    public static function dateIntervalToMicroseconds(
        \DateInterval $interval,
        BigNumber|int|float|string $yearSeconds = Duration::YEAR_SECONDS_COMMON,
        BigNumber|int|float|string $monthSeconds = Duration::MONTH_SECONDS_COMMON,
    ): BigDecimal {
        return static::dateIntervalToSeconds($interval, $yearSeconds, $monthSeconds)
            ->multipliedBy('1000000')
            ->plus(BigDecimal::of($interval->f)->multipliedBy('1000000'));
    }
}
