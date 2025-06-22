<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Compound;

use Asika\UnitConverter\AbstractConverter;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

abstract class AbstractCompoundConverter extends AbstractConverter
{
    public AbstractConverter $measure;
    public AbstractConverter $deno;

    public string $atomUnit {
        get => $this->measure->atomUnit;
    }

    public string $defaultUnit {
        get => $this->measure->defaultUnit;
    }

    public string $baseUnit {
        set {
            [$measureUnit, $denoUnit] = $this->normalizeAndSplitUnit($value);

            $this->baseUnit = $measureUnit;

            if ($denoUnit) {
                $this->deno = $this->deno->withBaseUnit($denoUnit);
            }
        }
    }

    protected array $unitExchanges {
        get => $this->measure->availableUnitExchanges;
    }

    #[\Override]
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
            [$measure, $deno] = $this->normalizeAndSplitUnit($unit);

            $measure = $this->normalizeUnit($measure);
            $deno = $this->deno->normalizeUnit($deno);

            $this->deno = $this->deno->with(1, $deno);

            $converted = $instance->withValue($val, $measure, $scale, $roundingMode)->value;

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

    #[\Override]
    public function convertTo(
        string $toUnit,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        [$measureUnit, $denoUnit] = $this->normalizeAndSplitUnit($toUnit);

        return $this->convertUnitPairTo(
            $measureUnit,
            $denoUnit,
            $scale,
            $roundingMode
        );
    }

    public function convertUnitPairTo(
        string $measureUnit,
        string $denoUnit = '',
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        $new = $this;

        // If we have a measure unit, we need to convert the value accordingly.
        if ($measureUnit) {
            $new = parent::convertTo($measureUnit, $scale, $roundingMode);
        }

        // If we have a denominator unit, we need to convert the value accordingly.
        if ($denoUnit && $denoUnit !== $this->deno->baseUnit) {
            // Make the deno as target unit.
            $new->deno = $new->deno->with(1, $denoUnit);

            // Convert the value to the base unit of the deno.
            $new->value = $new->deno->withValue($new->value)
                ->convertTo($this->deno->baseUnit, $scale, $roundingMode)
                ->value;
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

        return explode('/', $unit, 2) + ['', ''];
    }

    abstract protected function normalizeCompoundUnit(string $unit): string;

    #[\Override]
    protected function normalizeUnit(string $unit): string
    {
        return $this->measure->normalizeUnit($unit);
    }

    #[\Override]
    protected function formatSuffix(string $suffix, BigDecimal $value, string $unit): string
    {
        $suffix = parent::formatSuffix($suffix, $value, $unit);

        if ($suffix === $this->baseUnit) {
            $suffix .= '/' . $this->deno->formatSuffix($this->deno->baseUnit, $value, $this->deno->baseUnit);
        }

        return $suffix;
    }
}
