<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

abstract class AbstractBasicConverter extends AbstractConverter
{
    public const int OPTION_KEEP_ZERO = 1 << 0;

    public const int OPTION_NO_FALLBACK = 1 << 1;

    /**
     * @param  \Closure|string|array|null  $formats
     * @param  string                      $divider
     * @param  int                         $options
     *
     * @return  string
     */
    public function humanize(
        \Closure|string|array|null $formats = null,
        string $divider = ' ',
        int $options = 0,
    ): string {
        return $this->serializeCallback(
            function (self $remainder, array $sortedUnits) use ($options, $formats, $divider) {
                $formatter = null;
                $units = null;

                if ($formats instanceof \Closure || is_string($formats)) {
                    $formatter = $formats;
                } elseif (is_array($formats)) {
                    $units = $formats;
                }

                $unitFormatters = [];

                if ($units !== null) {
                    foreach ($units as $i => $unit) {
                        if (is_numeric($i)) {
                            $unitFormatters[$unit] = $formatter ?? $unit;
                        } else {
                            $unitFormatters[$i] = $formatter ?? $unit;
                        }
                    }

                    $unitFormatters = array_intersect_key(
                        $unitFormatters,
                        $sortedUnits,
                    );

                    if (empty($unitFormatters)) {
                        throw new \InvalidArgumentException('No valid units provided for humanization.');
                    }
                } else {
                    foreach (array_keys($sortedUnits) as $i => $unit) {
                        if (is_numeric($i)) {
                            $unitFormatters[$unit] = $formatter ?? $unit;
                        } else {
                            $unitFormatters[$i] = $formatter ?? $unit;
                        }
                    }
                }

                $text = [];

                foreach ($unitFormatters as $unit => $suffixFormat) {
                    $part = $remainder->extract($unit);

                    if (($options & static::OPTION_KEEP_ZERO) || !$part->isZero()) {
                        $text[] = $part->format($suffixFormat, $unit);
                    }
                }

                $formatted = trim(implode($divider, array_filter($text)));

                if (!$formatted && !($options & static::OPTION_NO_FALLBACK)) {
                    $minSuffix = $unitFormatters[$this->baseUnit];
                    $formatted = $this->with(0, $this->baseUnit)->format($minSuffix);
                }

                return $formatted;
            }
        );
    }

    public function nearest(
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN,
        ?array $units = null,
    ): static {
        $sortedUnits = $this->getSortedUnitRates();

        if ($units !== null) {
            $sortedUnits = array_intersect_key($sortedUnits, array_flip($units));
        }

        $closestValue = $this->value;
        $closestUnit = $this->baseUnit;
        $minDistance = null;

        foreach ($sortedUnits as $unit => $rate) {
            $converted = $this->to($unit, $scale, $roundingMode);

            if ($converted->isZero()) {
                continue;
            }

            $abs = $converted->abs();

            if ($abs->isLessThan(1)) {
                $distance = BigDecimal::of(1)->dividedBy($abs, $scale, RoundingMode::HALF_UP);
            } else {
                $distance = $abs->dividedBy(1, $scale, RoundingMode::HALF_UP);
            }

            if ($minDistance === null || $distance->isLessThan($minDistance)) {
                $minDistance = $distance;
                $closestUnit = $unit;
                $closestValue = $converted;
            }
        }

        $new = clone $this;
        $new->value = $closestValue;
        $new->baseUnit = $closestUnit;

        return $new;
    }
}
