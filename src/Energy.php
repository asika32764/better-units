<?php

declare(strict_types=1);

namespace Asika\BetterUnits;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toJoules(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilojoules(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMegajoules(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGigajoules(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTerajoules(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toCalories(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilocalories(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toNewtonMeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toVolts(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMegavolts(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toFootPounds(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toWattHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilowattHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMegawattHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGigawattHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTerawattHours(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
class Energy extends AbstractBasicMeasurement
{
    public const string UNIT_JOULE = 'j';

    public const string UNIT_KILOJOULE = 'kj';

    public const string UNIT_MEGAJOULE = 'mj';

    public const string UNIT_GIGAJOULE = 'gj';

    public const string UNIT_TERAJOULE = 'tj';

    public const string UNIT_CALORIE = 'cal';

    public const string UNIT_KILOCALORIE = 'kcal';

    public const string UNIT_NEWTON_METER = 'nm';

    public const string UNIT_VOLT = 'ev';

    public const string UNIT_MEGAVOLT = 'mev';

    public const string UNIT_FOOT_POUND = 'ftlb';

    public const string UNIT_WATT_HOUR = 'wh';

    public const string UNIT_KILOWATT_HOUR = 'kwh';

    public const string UNIT_MEGAWATT_HOUR = 'mwh';

    public const string UNIT_GIGAWATT_HOUR = 'gwh';

    public const string UNIT_TERAWATT_HOUR = 'twh';

    public const array UNITS_GROUP_JOULES = [
        self::UNIT_JOULE,
        self::UNIT_KILOJOULE,
        self::UNIT_MEGAJOULE,
        self::UNIT_GIGAJOULE,
        self::UNIT_TERAJOULE,
    ];

    public const array UNITS_GROUP_WATT_HOURS = [
        self::UNIT_WATT_HOUR,
        self::UNIT_KILOWATT_HOUR,
        self::UNIT_MEGAWATT_HOUR,
        self::UNIT_GIGAWATT_HOUR,
        self::UNIT_TERAWATT_HOUR,
    ];

    public string $atomUnit = self::UNIT_JOULE;

    public string $defaultUnit = self::UNIT_JOULE;

    protected array $unitExchanges = [
        self::UNIT_JOULE => 1.0,
        self::UNIT_KILOJOULE => 1000.0,
        self::UNIT_MEGAJOULE => 1e6,
        self::UNIT_GIGAJOULE => 1e9,
        self::UNIT_TERAJOULE => 1e12,
        self::UNIT_CALORIE => 4.184,
        self::UNIT_KILOCALORIE => 4184.0,
        self::UNIT_NEWTON_METER => 1.0,
        self::UNIT_VOLT => 1.602176634e-19,
        self::UNIT_MEGAVOLT => 1.602176634e-13,
        self::UNIT_FOOT_POUND => 1.3558179483314004,
        self::UNIT_WATT_HOUR => 3600.0,
        self::UNIT_KILOWATT_HOUR => 3.6e6,
        self::UNIT_MEGAWATT_HOUR => 3.6e9,
        self::UNIT_GIGAWATT_HOUR => 3.6e12,
        self::UNIT_TERAWATT_HOUR => 3.6e15,
    ];

    protected function normalizeUnit(string $unit): string
    {
        $unit = (string) str_replace(
            ['electron volt', 'electron volts', 'electronvolt', 'electronvolts'],
            'ev',
            $unit
        );

        $unit = match (strtolower($unit)) {
            'j', 'joule', 'joules' => self::UNIT_JOULE,
            'kj', 'kilojoule', 'kilojoules' => self::UNIT_KILOJOULE,
            'mj', 'megajoule', 'megajoules' => self::UNIT_MEGAJOULE,
            'gj', 'gigajoule', 'gigajoules' => self::UNIT_GIGAJOULE,
            'tj', 'terajoule', 'terajoules' => self::UNIT_TERAJOULE,
            'cal', 'calorie', 'calories' => self::UNIT_CALORIE,
            'kcal', 'kilocalorie', 'kilocalories' => self::UNIT_KILOCALORIE,
            'nm', 'newton meter', 'newton meters' => self::UNIT_NEWTON_METER,
            'ev', 'volt', 'volts' => self::UNIT_VOLT,
            'mev', 'megavolt', 'megavolts' => self::UNIT_MEGAVOLT,
            'ftlb', 'foot pound', 'foot pounds' => self::UNIT_FOOT_POUND,
            'wh', 'watt hour', 'watt hours' => self::UNIT_WATT_HOUR,
            'kwh', 'kilowatt hour', 'kilowatt hours', 'kwhr' => self::UNIT_KILOWATT_HOUR,
            'mwh', 'megawatt hour', 'megawatt hours', 'mwhr' => self::UNIT_MEGAWATT_HOUR,
            'gwh', 'gigawatt hour', 'gigawatt hours', 'gwhr' => self::UNIT_GIGAWATT_HOUR,
            'twh', 'terawatt hour', 'terawatt hours', 'twhr' => self::UNIT_TERAWATT_HOUR,
            default => $unit
        };

        return parent::normalizeUnit($unit);
    }

    public function withOnlyJouleUnits(): static
    {
        $new = $this->withAvailableUnits(static::UNITS_GROUP_JOULES);
        $new->atomUnit = self::UNIT_JOULE;
        $new->defaultUnit = self::UNIT_JOULE;

        return $new;
    }
}
