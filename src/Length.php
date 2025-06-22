<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toFemtometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPicometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toNanometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMicrometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMillimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toCentimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toDecimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toInches(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toFeet(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYards(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toHands(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMiles(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toLightYears(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toAstronomicalUnits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toParsecs(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toFathoms(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toNauticalMiles(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
class Length extends AbstractUnitConverter
{
    public const string UNIT_FEMTOMETERS = 'fm';
    public const string UNIT_PICOMETERS = 'pm';
    public const string UNIT_NANOMETERS = 'nm';
    public const string UNIT_MICROMETERS = 'μm';
    public const string UNIT_MILLIMETERS = 'mm';
    public const string UNIT_CENTIMETERS = 'cm';
    public const string UNIT_DECIMETERS = 'dm';
    public const string UNIT_METERS = 'm';
    public const string UNIT_KILOMETERS = 'km';
    public const string UNIT_INCHES = 'in';
    public const string UNIT_FEET = 'ft';
    public const string UNIT_YARDS = 'yd';
    public const string UNIT_HANDS = 'h';
    public const string UNIT_MILES = 'mi';
    public const string UNIT_LIGHT_YEARS = 'ly';
    public const string UNIT_ASTRONOMICAL_UNITS = 'au';
    public const string UNIT_PARSEC = 'pc';
    public const string UNIT_FATHOMS = 'fth';
    public const string UNIT_NAUTICAL_MILES = 'nmi';

    public const array UNITS_GROUP_COMMON_LENGTHS = [
        self::UNIT_FEMTOMETERS,
        self::UNIT_PICOMETERS,
        self::UNIT_NANOMETERS,
        self::UNIT_MICROMETERS,
        self::UNIT_MILLIMETERS,
        self::UNIT_CENTIMETERS,
        self::UNIT_DECIMETERS,
        self::UNIT_METERS,
        self::UNIT_KILOMETERS,
    ];

    public string $atomUnit = self::UNIT_PICOMETERS;

    public string $defaultUnit = self::UNIT_METERS;

    protected array $unitExchanges = [
        self::UNIT_FEMTOMETERS => 1e-15,
        self::UNIT_PICOMETERS => 1e-12,
        self::UNIT_NANOMETERS => 1e-9,
        self::UNIT_MICROMETERS => 1e-6,
        self::UNIT_MILLIMETERS => 1e-3,
        self::UNIT_CENTIMETERS => 0.01,
        self::UNIT_DECIMETERS => 0.1,
        self::UNIT_METERS => 1.0,
        self::UNIT_KILOMETERS => 1000.0,
        self::UNIT_INCHES => 0.0254,
        self::UNIT_FEET => 0.3048,
        self::UNIT_YARDS => 0.9144,
        self::UNIT_HANDS => 0.1016,
        self::UNIT_MILES => 1609.344,
        self::UNIT_LIGHT_YEARS => 9.461e15,
        self::UNIT_ASTRONOMICAL_UNITS => 149597870700.0,
        self::UNIT_PARSEC => 3.085677581491367e16,
        self::UNIT_FATHOMS => 1.8288,
        self::UNIT_NAUTICAL_MILES => 1852,
    ];

    protected function normalizeBaseUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'femtometer', 'femtometers' => self::UNIT_FEMTOMETERS,
            'picometer', 'picometers' => self::UNIT_PICOMETERS,
            'nanometer', 'nanometers' => self::UNIT_NANOMETERS,
            'micrometer', 'micrometers', 'um' => self::UNIT_MICROMETERS,
            'millimeter', 'millimeters' => self::UNIT_MILLIMETERS,
            'centimeter', 'centimeters' => self::UNIT_CENTIMETERS,
            'decimeter', 'decimeters' => self::UNIT_DECIMETERS,
            'meter', 'meters' => self::UNIT_METERS,
            'kilometer', 'kilometers' => self::UNIT_KILOMETERS,
            'inch', 'inches' => self::UNIT_INCHES,
            'foot', 'feet' => self::UNIT_FEET,
            'yard', 'yards' => self::UNIT_YARDS,
            'hand', 'hands' => self::UNIT_HANDS,
            'mile', 'miles' => self::UNIT_MILES,
            'light year', 'light years' => self::UNIT_LIGHT_YEARS,
            'astronomical unit', 'astronomical units' => self::UNIT_ASTRONOMICAL_UNITS,
            'parsec', 'parsecs' => self::UNIT_PARSEC,
            'fathom', 'fathoms' => self::UNIT_FATHOMS,
            'nautical mile', 'nautical miles' => self::UNIT_NAUTICAL_MILES,
            default => $unit,
        };
    }

    public function withOnlyCommonLengths(): static
    {
        return $this->withAvailableUnits(static::UNITS_GROUP_COMMON_LENGTHS);
    }
}
