<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Compound;

use Asika\BetterUnits\AbstractMeasurement;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

abstract class AbstractCompoundMeasurement extends AbstractMeasurement
{
    abstract public AbstractMeasurement $num {
        get;
    }
    abstract public AbstractMeasurement $deno {
        get;
    }

    protected array $unitExchanges {
        get => $this->compoundUnitExchanges;
    }

    abstract protected array $compoundUnitExchanges {
        get;
    }

    public protected(set) string $unit {
        set {
            [$numUnit, $denoUnit] = $this->normalizeAndSplitUnit($value);

            $this->unit = $this->normalizeCompoundUnit($value);
            $this->num = $this->num->withUnit($numUnit);

            if ($denoUnit) {
                $this->deno = $this->deno->withUnit($denoUnit);
            }
        }
    }

    /**
     * Some conversion needs 2-steps to convert the value to the atom unit.
     * This is the scale used for the intermediate conversion that can prevent
     * loss of precision.
     *
     * @var int
     */
    public protected(set) int $intermediateScale = 99;

    #[\Override]
    public function withParse(
        string $value,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $values = static::parseValue($value);

        $atomValue = BigDecimal::zero();
        $atomUnit = $this->num->atomUnit . '/' . $this->deno->atomUnit;
        $new = $this->with(0, $atomUnit);
        $calculatingScale = max($this->intermediateScale, $scale);

        foreach ($values as [$val, $unit]) {
            [, $denoUnit] = $new->normalizeAndSplitUnit($unit);
            $unit = $this->normalizeCompoundUnit($unit);

            $new = $this->with($val, $unit);

            // Maybe is a single unit name, like `mph` or `knots`.
            // We must use 2-steps conversion to convert it to the atom unit.
            // Convert to local atom unit first, local unit should be X/y format.
            // That can be referenced to child units.
            if (!$denoUnit) {
                $new = $new->convertTo(
                    $this->atomUnit,
                    $calculatingScale,
                    $roundingMode
                );
            }

            $converted = $new->convertTo(
                $atomUnit,
                $scale,
                $roundingMode
            );

            $atomValue = $atomValue->plus($converted->value);
        }

        $new = $new->with($atomValue, $atomUnit);

        $asUnit ??= $this->unit;

        if ($asUnit && $asUnit !== $new->unit) {
            $asUnit = $this->normalizeUnit($asUnit);
            $new = $new->convertTo($asUnit, $scale, $roundingMode);
        }

        return $new;
    }

    #[\Override]
    public function convertTo(
        string $toUnit,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $toUnit = $this->normalizeCompoundUnit($toUnit);
        $new = clone $this;

        $calculatingScale = max($this->intermediateScale, $scale);

        // Direct exchange. For example, k/m to kph.
        // Directly use the exchange rate if available.
        if ($toUnit !== $this->atomUnit && $this->getUnitExchangeRate($toUnit) !== null) {
            $new = $new->convertTo($this->atomUnit, $calculatingScale, $roundingMode);
            $newValue = $new->convertValue(
                $new->value,
                $new->atomUnit,
                $toUnit,
                $scale,
                $roundingMode
            );
            return $new->with($newValue, $toUnit);
        }

        $fromUnit = $this->normalizeCompoundUnit($this->unit);

        // Direct exchange. For example, mph to km/s.
        // Directly use the exchange rate if available.
        if ($fromUnit !== $this->atomUnit && $this->getUnitExchangeRate($fromUnit) !== null) {
            // Convert the value to the local compound atom unit first,
            // because the local atom unit can be referenced to child units.
            $newValue = $new->convertValue(
                $new->value,
                $fromUnit,
                $new->atomUnit,
                $calculatingScale,
                $roundingMode
            );

            // Now convert the value to the target child unit (X/y).
            return $new->with($newValue, $new->atomUnit)
                ->convertTo($toUnit, $scale, $roundingMode);
        }

        // Compound exchange, for example, m/s to km/h.
        // Must split the unit into numerator and denominator to exchange them.
        [$numUnit, $denoUnit] = $new->normalizeAndSplitUnit($toUnit);

        return $new->convertUnitPairTo(
            $numUnit,
            $denoUnit,
            $scale,
            $roundingMode
        );
    }

    public function convertUnitPairTo(
        string $numUnit,
        string $denoUnit = '',
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $new = $this;

        $calculatingScale = max($this->intermediateScale, $scale);

        // If we have a num unit, we need to convert the value accordingly.
        if ($numUnit) {
            $this->num = $this->num->with($new->value)
                ->convertTo($numUnit, $calculatingScale, $roundingMode);

            $new = $this->withValue($this->num->value);
            $new->unit = $numUnit;
        }

        // If we have a denominator unit, we need to convert the value accordingly.
        if ($denoUnit && $denoUnit !== $this->deno->unit) {
            // Make the deno as target unit.
            $new->deno = $new->deno->with(1, $denoUnit);

            // Convert the value to the base unit of the deno.
            $new->value = $new->deno->withValue($new->value)
                ->convertTo($this->deno->unit, $calculatingScale, $roundingMode)
                ->value;
        }

        if ($scale !== null) {
            $new->value = $new->value->toScale($scale, $roundingMode);
        } else {
            $new->value = $new->value->stripTrailingZeros();
        }

        return $new;
    }

    /**
     * @param  string  $unit
     *
     * @return  string[]
     */
    public function normalizeAndSplitUnit(string $unit): array
    {
        $unit = $this->normalizeCompoundUnit($unit);

        $units = explode('/', $unit, 2) + ['', ''];

        $units[0] = $this->num->normalizeUnit($units[0]);
        $units[1] = $this->deno->normalizeUnit($units[1]);

        return $units;
    }

    abstract protected function normalizeCompoundUnit(string $unit): string;

    #[\Override]
    protected function normalizeUnit(string $unit): string
    {
        return $this->normalizeCompoundUnit($unit);

        // return implode('/', array_filter($units));
    }

    #[\Override]
    public function format(
        string|\Closure|null $suffix = null,
        ?string $unit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): string {
        $addDenoSuffix = $suffix === null;

        if (!$suffix) {
            $suffix = $unit ?? $this->unit;

            if ($this->num->getUnitExchangeRate($suffix) !== null) {
                $suffix .= '/' . $this->deno->formatSuffix($this->deno->unit, $this->value, $this->deno->unit);
            }
        }

        $text = parent::format($suffix, $unit, $scale, $roundingMode);

        if ($addDenoSuffix && $this->num->getUnitExchangeRate($suffix) !== null) {
            $text .= '/' . $this->deno->formatSuffix($this->deno->unit, $this->value, $this->deno->unit);
        }

        return $text;
    }

    public function withIntermediateScale(int $intermediateScale): static
    {
        $new = clone $this;
        $new->intermediateScale = $intermediateScale;

        return $new;
    }

    public function __call(string $name, array $args)
    {
        if (str_starts_with($name, 'to')) {
            $unit = strtolower(substr($name, 2));
            $unit = str_replace(['per', '_'], ['/', ''], $unit);

            [$numUnit, $denoUnit] = $this->normalizeAndSplitUnit($unit);

            if ($this->num->getUnitExchangeRate($numUnit) && $this->deno->getUnitExchangeRate($denoUnit)) {
                return $this->to($unit, ...$args);
            }
        }

        return parent::__call($name, $args);
    }
}
