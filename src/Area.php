<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toSquareFemtometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquarePicometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareNanometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareMicrometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareMillimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareCentimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareDecimeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareMeters(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareKilometers(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareInches(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareFeet(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareYards(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareMiles(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareAcres(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toSquareHectares(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
class Area extends AbstractBasicMeasurement
{
    public const string UNIT_SQUARE_FEMTOMETERS = 'fm2';
    public const string UNIT_SQUARE_PICOMETERS = 'pm2';
    public const string UNIT_SQUARE_NANOMETERS = 'nm2';
    public const string UNIT_SQUARE_MICROMETERS = 'μm2';
    public const string UNIT_SQUARE_MILLIMETERS = 'mm2';
    public const string UNIT_SQUARE_CENTIMETERS = 'cm2';
    public const string UNIT_SQUARE_DECIMETERS = 'dm2';
    public const string UNIT_SQUARE_METERS = 'm2';
    public const string UNIT_SQUARE_KILOMETERS = 'km2';
    public const string UNIT_SQUARE_INCHES = 'in2';
    public const string UNIT_SQUARE_FEET = 'ft2';
    public const string UNIT_SQUARE_YARDS = 'yd2';
    public const string UNIT_SQUARE_MILES = 'mi2';
    public const string UNIT_SQUARE_ACRES = 'ac';
    public const string UNIT_SQUARE_HECTARES = 'ha';

    public const array UNITS_GROUP_COMMON_AREAS = [
        self::UNIT_SQUARE_FEMTOMETERS,
        self::UNIT_SQUARE_PICOMETERS,
        self::UNIT_SQUARE_NANOMETERS,
        self::UNIT_SQUARE_MICROMETERS,
        self::UNIT_SQUARE_MILLIMETERS,
        self::UNIT_SQUARE_CENTIMETERS,
        self::UNIT_SQUARE_DECIMETERS,
        self::UNIT_SQUARE_METERS,
        self::UNIT_SQUARE_KILOMETERS,
    ];

    public string $atomUnit = self::UNIT_SQUARE_PICOMETERS;

    public string $defaultUnit = self::UNIT_SQUARE_METERS;

    protected array $unitExchanges = [
        self::UNIT_SQUARE_FEMTOMETERS => 1e-30,
        self::UNIT_SQUARE_PICOMETERS => 1e-24,
        self::UNIT_SQUARE_NANOMETERS => 1e-18,
        self::UNIT_SQUARE_MICROMETERS => 1e-12,
        self::UNIT_SQUARE_MILLIMETERS => 1e-6,
        self::UNIT_SQUARE_CENTIMETERS => 1e-4,
        self::UNIT_SQUARE_DECIMETERS => 0.01,
        self::UNIT_SQUARE_METERS => 1.0,
        self::UNIT_SQUARE_KILOMETERS => 1e6,
        self::UNIT_SQUARE_INCHES => 0.00064516,
        self::UNIT_SQUARE_FEET => 0.09290304,
        self::UNIT_SQUARE_YARDS => 0.83612736,
        self::UNIT_SQUARE_MILES => 2589988.110336,
        self::UNIT_SQUARE_ACRES => 4046.8564224,
        self::UNIT_SQUARE_HECTARES => 10000.0,
    ];

    protected function normalizeUnit(string $unit): string
    {
        if (str_starts_with(strtolower($unit), 'square')) {
            $unit = trim(substr($unit, 6));
            $unit .= '^2';
        }

        $unit = match (strtolower($unit)) {
            'fm^2', 'fm²', 'femtometers2', 'femtometers^2', 'femtometers²' => self::UNIT_SQUARE_FEMTOMETERS,
            'pm^2', 'pm²', 'picometers2', 'picometers^2', 'picometers²' => self::UNIT_SQUARE_PICOMETERS,
            'nm^2', 'nm²', 'nanometers2', 'nanometers^2', 'nanometers²' => self::UNIT_SQUARE_NANOMETERS,
            'μm^2', 'μm²', 'um2', 'um^2', 'um²','micrometers2', 'micrometers^2', 'micrometers²' => self::UNIT_SQUARE_MICROMETERS,
            'mm^2', 'mm²', 'millimeters2', 'millimeters^2', 'millimeters²' => self::UNIT_SQUARE_MILLIMETERS,
            'cm^2', 'cm²', 'centimeters2', 'centimeters^2', 'centimeters²' => self::UNIT_SQUARE_CENTIMETERS,
            'dm^2', 'dm²', 'decimeters2', 'decimeters^2', 'decimeters²' => self::UNIT_SQUARE_DECIMETERS,
            'm^2', 'm²', 'meters2', 'meters^2', 'meters²' => self::UNIT_SQUARE_METERS,
            'km^2', 'km²', 'kilometers2', 'kilometers^2', 'kilometers²' => self::UNIT_SQUARE_KILOMETERS,
            'in^2', 'in²', 'inches2', 'inches^2', 'inches²' => self::UNIT_SQUARE_INCHES,
            'ft^2', 'ft²', 'feet2', 'feet^2', 'feet²' => self::UNIT_SQUARE_FEET,
            'yd^2', 'yd²', 'yards2', 'yards^2', 'yards²' => self::UNIT_SQUARE_YARDS,
            'mi^2', 'mi²', 'miles2', 'miles^2', 'miles²' => self::UNIT_SQUARE_MILES,
            'ac', 'acre', 'acres' => self::UNIT_SQUARE_ACRES,
            'ha', 'hectare', 'hectares' => self::UNIT_SQUARE_HECTARES,
            default => $unit,
        };

        return parent::normalizeUnit($unit);
    }

    public function withOnlyCommonAreas(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_COMMON_AREAS);
    }
}
