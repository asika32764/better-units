<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;

/**
 * @formatter:off
 *
 * @psalm-type HumanizeCallback = \Closure(AbstractUnitConverter $remainder, array<string, BigDecimal> $sortedUnits): string
 * @psalm-type FormatterCallback = \Closure(BigDecimal $value, AbstractUnitConverter $converter): string
 *
 * @formatter:on
 */
abstract class AbstractUnitConverter
{
    public const int KEEP_ZERO = 1 << 0;
    public const int WITHOUT_FALLBACK = 1 << 1;

    public BigNumber $value {
        set(mixed $value) => $this->value = BigDecimal::of($value);
    }

    public string $baseUnit;

    abstract public string $atomUnit {
        get;
    }

    abstract public string $defaultUnit {
        get;
    }

    abstract protected array $unitExchanges {
        get;
    }

    public ?\Closure $unitNormalizer = null;

    public static function from(mixed $value, ?string $baseUnit = null): static
    {
        if (is_string($value) && !is_numeric($value)) {
            return static::parse($value, $baseUnit);
        }

        return new static($value, $baseUnit);
    }

    public static function parse(mixed $value, ?string $asUnit = null): static
    {
        return new static()->withParse($value, $asUnit);
    }

    public static function parseToValue(mixed $value, ?string $asUnit = null): BigDecimal
    {
        return static::parse($value, $asUnit)->value->toBigDecimal();
    }

    public function withParse(
        string $value,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $instance = $this->with(0, $this->atomUnit);

        $values = static::parseValue($value);

        $nanoSeconds = BigDecimal::zero();

        foreach ($values as [$val, $unit]) {
            $unit = $this->normalizeUnit($unit);
            $converted = $instance->withValue($val, $unit, $scale, $roundingMode)->value;

            $nanoSeconds = $nanoSeconds->plus($converted);
        }

        $instance = $instance->withValue($nanoSeconds);

        $asUnit ??= $this->baseUnit;

        if ($asUnit && $asUnit !== $instance->baseUnit) {
            $asUnit = $this->normalizeUnit($asUnit);
            $instance = $instance->convertTo($asUnit, $scale, $roundingMode);
        }

        return $instance;
    }

    public function __construct(mixed $value = 0, ?string $baseUnit = null)
    {
        $this->value = BigNumber::of($value);
        $this->baseUnit = $baseUnit ?? $this->defaultUnit;
    }

    /**
     * A quick convert without creating an instance.
     *
     * @param  mixed         $value
     * @param  string        $fromUnit
     * @param  string        $toUnit
     * @param  int|null      $scale
     * @param  RoundingMode  $roundingMode
     *
     * @return  BigDecimal
     *
     * @throws \Brick\Math\Exception\DivisionByZeroException
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Math\Exception\NumberFormatException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     */
    public static function convert(
        mixed $value,
        string $fromUnit,
        string $toUnit,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): BigNumber {
        return new static($value, $fromUnit)
            ->convertTo(
                $toUnit,
                $scale,
                $roundingMode
            )->value;
    }

    public function convertTo(
        string $toUnit,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $toUnit = $this->normalizeUnit($toUnit);

        if ($toUnit === $this->baseUnit) {
            return $this;
        }

        $new = clone $this;

        $newValue = $this->value;

        if (!$newValue->isZero()) {
            $fromUnitRate = $this->getUnitExchangeRate($this->baseUnit)
                ?? throw new \InvalidArgumentException("Unknown base unit: {$this->baseUnit}");

            $toUnitRate = $this->getUnitExchangeRate($toUnit)
                ?? throw new \InvalidArgumentException("Unknown target unit: {$toUnit}");

            $newValue = BigDecimal::of($this->value)
                ->multipliedBy($fromUnitRate)
                ->dividedBy($toUnitRate, $scale, $roundingMode);
        }

        $new->value = $newValue;
        $new->baseUnit = $toUnit;

        return $new;
    }

    public function withValue(
        mixed $value,
        ?string $fromUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $new = clone $this;
        $new->value = $value;
        $new->baseUnit = $fromUnit ? $this->normalizeUnit($fromUnit) : $this->baseUnit;

        if ($new->baseUnit !== $this->baseUnit) {
            $new = $new->convertTo($this->baseUnit, $scale, $roundingMode);
        }

        return $new;
    }

    public function withBaseUnit(string $unit): static
    {
        $new = clone $this;
        $new->baseUnit = $this->normalizeUnit($unit);

        return $new;
    }

    public function with(mixed $value, ?string $baseUnit = null): static
    {
        $new = clone $this;
        $new->value = $value;
        $new->baseUnit = $baseUnit ? $this->normalizeUnit($baseUnit) : $this->baseUnit;

        return $new;
    }

    /**
     * @param  FormatterCallback|string|null  $suffix
     * @param  string|null                    $unit
     * @param  int|null                       $scale
     * @param  RoundingMode                   $roundingMode
     *
     * @return  string
     *
     * @throws RoundingNecessaryException
     */
    public function format(
        \Closure|string|null $suffix = null,
        ?string $unit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): string {
        if ($unit !== null) {
            $unit = $this->normalizeUnit($unit);
            $new = $this->convertTo($unit, $scale, $roundingMode);
        } else {
            $new = $this;
        }

        $value = $new->value;

        if ($scale !== null) {
            $value = $value->toScale($scale, $roundingMode);
        } else {
            $value = $value->stripTrailingZeros();
        }

        $suffix ??= $unit ?? $this->baseUnit;

        if ($suffix instanceof \Closure) {
            return $suffix($value, $this);
        }

        if (is_string($suffix) && str_contains($suffix, '%')) {
            return sprintf($suffix, $value);
        }

        return $value . $suffix;
    }

    public function isZero(): bool
    {
        return $this->value->isZero();
    }

    public function isNegative(): bool
    {
        return $this->value->isNegative();
    }

    /**
     * @param  string  $unit
     *
     * @return  array{ static, static }
     */
    public function withExtract(string $unit): array
    {
        $new = clone $this;

        return [$new->extract($unit), $new];
    }

    private function extract(string $unit): static
    {
        $rate = $this->with(1, $unit)->convertTo($this->baseUnit)->value;

        /** @var BigDecimal $part */
        $part = $this->value->dividedBy($rate, 0, RoundingMode::DOWN);

        $this->value = $this->value->minus($part->multipliedBy($rate));

        return $this->with($part, $unit);
    }

    /**
     * @param  HumanizeCallback|array|int|null  $units
     * @param  string                           $divider
     * @param  FormatterCallback|string|null    $formatter
     *
     * @return  string
     *
     * @throws RoundingNecessaryException
     */
    public function humanize(
        \Closure|array|int|null $options = null,
        string $divider = ' ',
        \Closure|string|null $formatter = null
    ): string {
        $atomUnit = $this->atomUnit;
        $remainder = $this->convertTo($atomUnit);

        if ($options instanceof \Closure) {
            return (string) $options($remainder, $this->getSortedUnits());
        }

        $units = null;

        if (!is_int($options)) {
            $units = $options;
        }

        if ($units === null) {
            $units = $this->getSortedUnits();

            $units = array_keys($units);
        }

        $unitFormatters = [];

        foreach ($units as $i => $unit) {
            if (is_numeric($i)) {
                $unitFormatters[$unit] = $formatter ?? $unit;
            } else {
                $unitFormatters[$i] = $formatter ?? $unit;
            }
        }

        $text = [];

        foreach ($unitFormatters as $unit => $suffixFormat) {
            $part = $remainder->extract($unit);

            if (($options & static::KEEP_ZERO) || !$part->isZero()) {
                $text[] = $part->format($suffixFormat, $unit);
            }
        }

        $formatted = trim(implode($divider, array_filter($text)));

        if (!$formatted && !($options & static::WITHOUT_FALLBACK)) {
            $minSuffix = $unitFormatters[$this->baseUnit];
            $formatted = $this->with(0, $this->baseUnit)->format($minSuffix);
        }

        return $formatted;
    }

    public function withAddedUnitExchangeRate(
        string $unit,
        BigNumber|float|int|string $rate,
        bool $prepend = false
    ): static {
        $new = clone $this;

        if ($prepend) {
            $new->unitExchanges = [
                $unit => $rate,
                ...$new->unitExchanges,
            ];
        } else {
            $new->unitExchanges[$unit] = $rate;
        }

        return $new;
    }

    public function withoutUnitExchangeRate(string $unit): static
    {
        $new = clone $this;
        unset($new->unitExchanges[$unit]);

        return $new;
    }

    public function getUnitExchangeRate(string $unit): ?BigNumber
    {
        $unit = $this->normalizeUnit($unit);

        if (isset($this->unitExchanges[$unit])) {
            return BigDecimal::of($this->unitExchanges[$unit]);
        }

        return null;
    }

    /**
     * @param  array<BigNumber|float|int>  $units
     * @param  string                      $defaultUnit
     *
     * @return  $this
     */
    public function withUnitExchanges(array $units, string $defaultUnit): static
    {
        $new = clone $this;
        $new->unitExchanges = $units;
        $new->defaultUnit = $defaultUnit;

        return $new;
    }

    public function to(string $unit, ?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN): BigNumber
    {
        return $this->convertTo($unit, $scale, $roundingMode)->value;
    }

    public function __call(string $name, array $args)
    {
        if (str_starts_with($name, 'to')) {
            $unit = strtolower(substr($name, 2));
            $unit = str_replace('_', '', $unit);

            if (array_key_exists($unit, $this->unitExchanges)) {
                return $this->to($unit, ...$args);
            }
        }

        throw new \BadMethodCallException("Method {$name} does not exist.");
    }

    /**
     * @param  string  $value
     *
     * @return  array<array{ value: string, unit: string }>
     */
    protected static function parseValue(string $value): array
    {
        preg_match_all('/((?P<value>[\d\.]+)\s*(?P<unit>[a-zA-Z]+))/i', $value, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            throw new \InvalidArgumentException("Invalid format: {$value}");
        }

        return array_map(
            static fn($match) => [$match['value'], $match['unit']],
            $matches
        );
    }

    abstract protected function normalizeBaseUnit(string $unit): string;

    protected function normalizeUnit(string $unit): string
    {
        $unit = $this->normalizeBaseUnit($unit);

        if ($this->unitNormalizer) {
            $unit = ($this->unitNormalizer)($unit);
        }

        return $unit;
    }

    /**
     * @return  array<string, BigDecimal>
     */
    protected function getSortedUnits(): array
    {
        $units = array_map(BigDecimal::of(...), $this->unitExchanges);

        uasort(
            $units,
            static fn(BigDecimal $a, BigDecimal $b) => $b->toFloat() <=> $a->toFloat(),
        );

        return $units;
    }
}
