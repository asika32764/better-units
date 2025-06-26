<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Asika\UnitConverter\Concerns\DurationCalendlyTrait;
use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

/**
 * The Duration class.
 *
 * @method BigDecimal toNanoseconds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMicroseconds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMilliseconds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSeconds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMinutes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toDays(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toWeeks(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMonths(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYears(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
// phpcs:disable
class Duration extends AbstractBasicConverter
{
    use DurationCalendlyTrait;

    public const string UNIT_FEMTOSECONDS = 'femtoseconds';

    public const string UNIT_PICOSECONDS = 'picoseconds';

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

    public string $atomUnit = self::UNIT_FEMTOSECONDS;

    public string $defaultUnit = self::UNIT_SECONDS;

    protected array $unitExchanges = [
        self::UNIT_FEMTOSECONDS => 1e-15,
        self::UNIT_PICOSECONDS => 1e-12,
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
    ] {
        get {
            $units = $this->unitExchanges;

            if (isset($units[self::UNIT_MONTHS])) {
                $units[self::UNIT_MONTHS] = $this->monthSeconds->toFloat();
            }

            if (isset($units[self::UNIT_YEARS])) {
                $units[self::UNIT_YEARS] = $this->yearSeconds->toFloat();
            }

            return $units;
        }
    }

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

    public static function fromDateInterval(
        \DateInterval $interval,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::HALF_UP
    ): static {
        return new static()->withFromDateInterval($interval, $asUnit, $scale, $roundingMode);
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

        return $this->withFromDateInterval($interval, $asUnit, $scale, $roundingMode);
    }

    public function withFromDateInterval(
        \DateInterval $interval,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::HALF_UP
    ): static {
        $microseconds = $this->intervalToMicroseconds($interval);

        $instance = $this->with($microseconds, static::UNIT_MICROSECONDS);

        $asUnit ??= $this->unit;

        if ($asUnit && $asUnit !== $instance->unit) {
            $asUnit = $this->normalizeUnit($asUnit);
            $instance = $instance->convertTo($asUnit, $scale, $roundingMode);
        }

        return $instance;
    }

    public function toDateInterval(): \DateInterval
    {
        $instance = $this->convertTo(static::UNIT_MICROSECONDS, 0, RoundingMode::HALF_UP);

        return \DateInterval::createFromDateString($instance->humanize());
    }

    public function intervalToSeconds(\DateInterval $interval): BigDecimal
    {
        return ConvertHelper::dateIntervalToSeconds($interval, $this->yearSeconds, $this->monthSeconds);
    }

    public function intervalToMicroseconds(\DateInterval $interval): BigDecimal
    {
        return ConvertHelper::dateIntervalToMicroseconds($interval, $this->yearSeconds, $this->monthSeconds);
    }

    /**
     * @template T of \DateTimeInterface
     *
     * @param  T|string  $now
     *
     * @return  T
     *
     * @throws \DateMalformedStringException
     */
    public function toFutureDateTime(\DateTimeInterface|string $now = 'now'): \DateTimeInterface
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTimeImmutable($now);
        }

        return $now->add($this->toDateInterval());
    }

    /**
     * @template T of \DateTimeInterface
     *
     * @param  T|string  $now
     *
     * @return  T
     *
     * @throws \DateMalformedStringException
     */
    public function toPastDateTime(\DateTimeInterface|string $now = 'now'): \DateTimeInterface
    {
        if (!$now instanceof \DateTimeInterface) {
            $now = new \DateTimeImmutable($now);
        }

        return $now->sub($this->toDateInterval());
    }

    public function withYearSeconds(BigNumber|string|int|float $yearSeconds): Duration
    {
        $new = clone $this;
        $new->yearSeconds = $yearSeconds;

        return $new;
    }

    public function withMonthSeconds(BigNumber|string|int|float $monthSeconds): Duration
    {
        $new = clone $this;
        $new->monthSeconds = $monthSeconds;

        return $new;
    }

    protected function formatSuffix(string $suffix, BigDecimal $value, string $unit): string
    {
        if ($value->abs()->isEqualTo(1)) {
            $suffix = match (strtolower($suffix)) {
                static::UNIT_NANOSECONDS => 'nanosecond',
                static::UNIT_MICROSECONDS => 'microsecond',
                static::UNIT_MILLISECONDS => 'millisecond',
                static::UNIT_SECONDS => 'second',
                static::UNIT_MINUTES => 'minute',
                static::UNIT_HOURS => 'hour',
                static::UNIT_DAYS => 'day',
                static::UNIT_WEEKS => 'week',
                static::UNIT_MONTHS => 'month',
                static::UNIT_YEARS => 'year',
                default => $suffix,
            };
        }

        return parent::formatSuffix($suffix, $value, $unit);
    }

    protected function normalizeUnit(string $unit): string
    {
        $unit = match (strtolower($unit)) {
            'fs', 'femtosecond', 'femtoseconds' => static::UNIT_FEMTOSECONDS,
            'ps', 'picosecond', 'picoseconds' => static::UNIT_PICOSECONDS,
            'ns', 'nanosecond', 'nanoseconds' => static::UNIT_NANOSECONDS,
            'us', 'μs', 'microsecond', 'microseconds' => static::UNIT_MICROSECONDS,
            'ms', 'millisecond', 'milliseconds' => static::UNIT_MILLISECONDS,
            's', 'second', 'sec', 'seconds' => static::UNIT_SECONDS,
            'm', 'minute', 'min', 'minutes' => static::UNIT_MINUTES,
            'h', 'hr', 'hour', 'hours' => static::UNIT_HOURS,
            'd', 'day', 'days' => static::UNIT_DAYS,
            'w', 'week', 'weeks' => static::UNIT_WEEKS,
            'mo', 'month', 'months' => static::UNIT_MONTHS,
            'y', 'year', 'years' => static::UNIT_YEARS,
            default => $unit,
        };

        return parent::normalizeUnit($unit);
    }

    public function withShortUnitFormatters(): static
    {
        return $this->withSuffixFormatter(
            fn(string $unit): string => match (strtolower($unit)) {
                Duration::UNIT_FEMTOSECONDS => 'fs',
                Duration::UNIT_PICOSECONDS => 'ps',
                Duration::UNIT_NANOSECONDS => 'ns',
                Duration::UNIT_MICROSECONDS => 'μs',
                Duration::UNIT_MILLISECONDS => 'ms',
                Duration::UNIT_SECONDS => 's',
                Duration::UNIT_MINUTES => 'min',
                Duration::UNIT_HOURS => 'h',
                Duration::UNIT_DAYS => 'd',
                Duration::UNIT_WEEKS => 'w',
                Duration::UNIT_MONTHS => 'mo',
                Duration::UNIT_YEARS => 'y',
                default => $unit,
            }
        );
    }
}
