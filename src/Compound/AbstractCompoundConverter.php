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

    public function convertTo(
        string $toUnit,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static {
        [$measureUnit, $denoUnit] = $this->normalizeAndSplitUnit($toUnit);

        $new = $this;

        if ($measureUnit) {
            $new = parent::convertTo($measureUnit, $scale, $roundingMode);
        }

        if ($denoUnit && $denoUnit !== $this->deno->baseUnit) {
            $oldUnit = $new->deno->baseUnit;

            $new->deno = $new->deno->with(1, $denoUnit);

            $new->value = $new->deno->convertTo($oldUnit, $scale, $roundingMode)
                ->value->multipliedBy($new->value);
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

    protected function normalizeUnit(string $unit): string
    {
        return $this->measure->normalizeUnit($unit);
    }

    public function format(
        string|\Closure|null $suffix = null,
        ?string $unit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): string {
        $unit ??= ($this->baseUnit . '/' . $this->deno->baseUnit);
        [$measureUnit, $denoUnit] = $this->normalizeAndSplitUnit($unit);

        $suffix ??= $measureUnit . '/' . $denoUnit;

        $suffix = $this->formatSuffix($suffix, $this->value, $unit);

        return parent::format($suffix, $measureUnit, $scale, $roundingMode);
    }
}
