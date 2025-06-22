<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Compound;

use Asika\UnitConverter\AbstractUnitConverter;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

abstract class AbstractCompoundUnitConverter extends AbstractUnitConverter
{
    public AbstractUnitConverter $measure;
    public AbstractUnitConverter $deno;

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

            $measure = $this->normalizeBaseUnit($measure);
            $deno = $this->deno->normalizeBaseUnit($deno);

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

        $instance = parent::convertTo($measureUnit, $scale, $roundingMode);

        if ($denoUnit !== $this->deno->baseUnit) {
            $this->deno = $this->deno->with(1)
                ->convertTo($denoUnit, $scale, $roundingMode);

            $instance->value = $instance->value->dividedBy($this->deno->value, $scale, $roundingMode);
        }

        return $instance;
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

    protected function normalizeBaseUnit(string $unit): string
    {
        return $this->measure->normalizeBaseUnit($unit);
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

        return parent::format($suffix, $measureUnit, $scale, $roundingMode);
    }
}
