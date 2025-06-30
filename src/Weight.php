<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toFemtograms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPicograms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toNanograms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMicrograms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMilligrams(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGrams(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toDecigrams(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toCentigrams(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilograms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMetricTons(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toOunces(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPounds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toStones(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTons(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toCarats(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toNewtons(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
class Weight extends AbstractBasicMeasurement
{
    public const string UNIT_FEMTOGRAMS = 'fg';

    public const string UNIT_PICOGRAMS = 'pg';

    public const string UNIT_NANOGRAMS = 'ng';

    public const string UNIT_MICROGRAMS = 'μg';

    public const string UNIT_MILLIGRAMS = 'mg';

    public const string UNIT_GRAMS = 'g';

    public const string UNIT_DECIGRAMS = 'dg';

    public const string UNIT_CENTIGRAMS = 'cg';

    public const string UNIT_KILOGRAMS = 'kg';

    public const string UNIT_METRIC_TONS = 't';

    public const string UNIT_OUNCES = 'oz';

    public const string UNIT_POUNDS = 'lb';

    public const string UNIT_STONES = 'st';

    public const string UNIT_TONS = 'tn';

    public const string UNIT_CARATS = 'ct';

    public const string UNIT_NEWTONS = 'N';

    public const array UNITS_GROUP_COMMON_WEIGHTS = [
        self::UNIT_FEMTOGRAMS,
        self::UNIT_PICOGRAMS,
        self::UNIT_NANOGRAMS,
        self::UNIT_MICROGRAMS,
        self::UNIT_MILLIGRAMS,
        self::UNIT_GRAMS,
        self::UNIT_DECIGRAMS,
        self::UNIT_CENTIGRAMS,
        self::UNIT_KILOGRAMS,
    ];

    public string $atomUnit = self::UNIT_FEMTOGRAMS;

    public string $defaultUnit = self::UNIT_GRAMS;

    protected array $unitExchanges = [
        self::UNIT_FEMTOGRAMS => 1e-15,
        self::UNIT_PICOGRAMS => 1e-12,
        self::UNIT_NANOGRAMS => 1e-9,
        self::UNIT_MICROGRAMS => 1e-6,
        self::UNIT_MILLIGRAMS => 1e-3,
        self::UNIT_GRAMS => 1.0,
        self::UNIT_DECIGRAMS => 0.1,
        self::UNIT_CENTIGRAMS => 0.01,
        self::UNIT_KILOGRAMS => 1000.0,
        self::UNIT_METRIC_TONS => 1e6,
        self::UNIT_OUNCES => 28.349523125,
        self::UNIT_POUNDS => 453.59237,
        self::UNIT_STONES => 6350.29318,
        self::UNIT_TONS => 907184.74,
        self::UNIT_CARATS => 0.2,
        self::UNIT_NEWTONS => 101.972,
    ] {
        get {
            $units = $this->unitExchanges;
            $units[self::UNIT_NEWTONS] = BigDecimal::of(1)
                ->dividedBy($this->gravityAcceleration, 3, RoundingMode::HALF_UP)
                ->toFloat();

            return $units;
        }
    }

    // Standard gravity in m/s²
    public protected(set) BigDecimal $gravityAcceleration {
        get => $this->gravityAcceleration ??= BigDecimal::of(9.80665);
    }

    protected function normalizeUnit(string $unit): string
    {
        $unit = match (strtolower($unit)) {
            'femtograms', 'femtogram', 'fg' => self::UNIT_FEMTOGRAMS,
            'picograms', 'picogram', 'pg' => self::UNIT_PICOGRAMS,
            'nanograms', 'nanogram', 'ng' => self::UNIT_NANOGRAMS,
            'micrograms', 'microgram', 'μg', 'mcg' => self::UNIT_MICROGRAMS,
            'milligrams', 'milligram', 'mg' => self::UNIT_MILLIGRAMS,
            'grams', 'gram', 'g' => self::UNIT_GRAMS,
            'decigrams', 'decigram', 'dg' => self::UNIT_DECIGRAMS,
            'centigrams', 'centigram', 'cg' => self::UNIT_CENTIGRAMS,
            'kilograms', 'kilogram', 'kg' => self::UNIT_KILOGRAMS,
            'metric tons', 'metric ton', 'metrictons', 'metricton', 'tonnes', 'tonne', 't' => self::UNIT_METRIC_TONS,
            'ounces', 'ounce', 'oz' => self::UNIT_OUNCES,
            'pounds', 'pound', 'lb' => self::UNIT_POUNDS,
            'stones', 'stone', 'st' => self::UNIT_STONES,
            'tons', 'ton', 'tn' => self::UNIT_TONS,
            'carats', 'carat', 'ct' => self::UNIT_CARATS,
            'newtons', 'newton', 'n' => self::UNIT_NEWTONS,
            default => $unit,
        };

        return parent::normalizeUnit($unit);
    }

    public function withOnlyCommonWeights(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_COMMON_WEIGHTS);
    }

    public function withGravityAcceleration(BigDecimal|float|int|string $gAcceleration): Weight
    {
        $this->gravityAcceleration = BigDecimal::of($gAcceleration);

        return $this;
    }
}
