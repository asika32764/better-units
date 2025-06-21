<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Asika\UnitConverter\Concerns\DurationCalendlyTrait;
use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

/**
 * The Duration class.
 *
 * @method BigDecimal toNanoseconds(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMicroseconds(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMilliseconds(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSeconds(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMinutes(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toHours(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toDays(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toWeeks(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMonths(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYears(int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
// phpcs:disable
class Duration extends AbstractUnitConverter
{
    use DurationCalendlyTrait;

    public const string UNIT_NANOSECONDS = 'nanoseconds';

    public const string UNIT_MICROSECONDS = 'microseconds';

    public const string UNIT_MILLISECONDS = 'milliseconds';

    public const string UNIT_SECONDS = 'seconds';

    public const string UNIT_MINUTES = 'minutes';

    public const string UNIT_HOURS = 'hours';

    public const string UNIT_DAYS = 'days';

    public const string UNIT_WEEKS = 'weeks';

    public const string UNIT_MONTHS = 'months';

    public const string UNIT_YEARS = 'years';

    public string $atomUnit = self::UNIT_NANOSECONDS;

    public string $defaultUnit = self::UNIT_SECONDS;

    protected array $unitExchanges = [
        self::UNIT_NANOSECONDS => 1e-9,
        self::UNIT_MICROSECONDS => 1e-6,
        self::UNIT_MILLISECONDS => 1e-3,
        self::UNIT_SECONDS => 1.0,
        self::UNIT_MINUTES => 60.0,
        self::UNIT_HOURS => 3600.0,
        self::UNIT_DAYS => 86400.0,
        self::UNIT_WEEKS => 604800.0,
        self::UNIT_MONTHS => self::MONTH_SECONDS_COMMON,
        self::UNIT_YEARS => self::YEAR_SECONDS_COMMON,
    ];

    public protected(set) BigNumber $yearSeconds {
        set(mixed $value) => $this->yearSeconds = BigNumber::of($value);
        get => $this->yearSeconds ??= BigNumber::of(self::YEAR_SECONDS_COMMON);
    }

    public protected(set) BigNumber $monthSeconds {
        set(mixed $value) => $this->monthSeconds = BigNumber::of($value);
        get => $this->monthSeconds ??= BigNumber::of(self::MONTH_SECONDS_COMMON);
    }

    // phpcs:enable

    /**
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public static function parseDateString(
        string $value,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::HALF_UP
    ): static {
        return new static()->withParseDateString($value, $asUnit, $scale, $roundingMode);
    }

    /**
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function withParseDateString(
        string $value,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::HALF_UP
    ): static {
        $interval = \DateInterval::createFromDateString($value);

        $microseconds = $this->intervalToMicroseconds($interval);

        $instance = $this->with($microseconds, static::UNIT_MICROSECONDS);

        $asUnit ??= $this->baseUnit;

        if ($asUnit && $asUnit !== $instance->baseUnit) {
            $asUnit = $this->normalizeUnit($asUnit);
            $instance = $instance->convertTo($asUnit, $scale, $roundingMode);
        }

        return $instance;
    }

    public function intervalToSeconds(\DateInterval $interval): BigDecimal
    {
        return ConvertHelper::dateIntervalToSeconds($interval, $this->yearSeconds, $this->monthSeconds);
    }

    public function intervalToMicroseconds(\DateInterval $interval): BigDecimal
    {
        return ConvertHelper::dateIntervalToMicroseconds($interval, $this->yearSeconds, $this->monthSeconds);
    }

    public function withYearSeconds(BigNumber|string|int|float $yearSeconds): Duration
    {
        $new = clone $this;
        $new->yearSeconds = $yearSeconds;

        $sRate = $this->getUnitExchangeRate(Duration::UNIT_SECONDS);

        if (!$sRate) {
            throw new \RuntimeException('Cannot set yearSeconds without seconds exchange rate.');
        }

        return $new->withAddedUnitExchangeRate(
            static::UNIT_YEARS,
            $sRate->multipliedBy($yearSeconds)
        );
    }

    public function withMonthSeconds(BigNumber|string|int|float $monthSeconds): Duration
    {
        $new = clone $this;
        $new->monthSeconds = $monthSeconds;

        $sRate = $this->getUnitExchangeRate(static::UNIT_SECONDS);

        if (!$sRate) {
            throw new \RuntimeException('Cannot set monthSeconds without seconds exchange rate.');
        }

        return $new->withAddedUnitExchangeRate(
            static::UNIT_MONTHS,
            $sRate->multipliedBy($monthSeconds)
        );
    }

    protected function normalizeBaseUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'ns', 'nanosecond' => self::UNIT_NANOSECONDS,
            'us', 'μs', 'microsecond' => self::UNIT_MICROSECONDS,
            'ms', 'millisecond' => self::UNIT_MILLISECONDS,
            's', 'second', 'sec' => self::UNIT_SECONDS,
            'm', 'minute', 'min' => self::UNIT_MINUTES,
            'h', 'hr', 'hour' => self::UNIT_HOURS,
            'd', 'day' => self::UNIT_DAYS,
            'w', 'week' => self::UNIT_WEEKS,
            'mo', 'month' => self::UNIT_MONTHS,
            'y', 'year' => self::UNIT_YEARS,
            default => $unit,
        };
    }
}
