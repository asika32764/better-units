<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

class Volume extends AbstractUnitConverter
{
    public const string UNIT_CUBIC_FEMTOMETERS = 'fm3';
    public const string UNIT_CUBIC_PICOMETERS = 'pm3';
    public const string UNIT_CUBIC_NANOMETERS = 'nm3';
    public const string UNIT_CUBIC_MICROMETERS = 'μm3';
    public const string UNIT_CUBIC_MILLIMETERS = 'mm3';
    public const string UNIT_CUBIC_CENTIMETERS = 'cm3';
    public const string UNIT_CUBIC_DECIMETERS = 'dm3';
    public const string UNIT_CUBIC_METERS = 'm3';
    public const string UNIT_CUBIC_KILOMETERS = 'km3';
    public const string UNIT_CUBIC_INCHES = 'in3';
    public const string UNIT_CUBIC_FEET = 'ft3';
    public const string UNIT_CUBIC_YARDS = 'yd3';
    public const string UNIT_CUBIC_MILES = 'mi3';
    public const string UNIT_CUBIC_LITERS = 'L';
    public const string UNIT_CUBIC_GALLONS = 'gal';
    public const string UNIT_CUBIC_PINTS = 'pt';
    public const string UNIT_CUBIC_QUARTS = 'qt';
    public const array UNITS_GROUP_COMMON_VOLUMES = [
        self::UNIT_CUBIC_FEMTOMETERS,
        self::UNIT_CUBIC_PICOMETERS,
        self::UNIT_CUBIC_NANOMETERS,
        self::UNIT_CUBIC_MICROMETERS,
        self::UNIT_CUBIC_MILLIMETERS,
        self::UNIT_CUBIC_CENTIMETERS,
        self::UNIT_CUBIC_DECIMETERS,
        self::UNIT_CUBIC_METERS,
        self::UNIT_CUBIC_KILOMETERS,
    ];

    public string $atomUnit = self::UNIT_CUBIC_PICOMETERS;

    public string $defaultUnit = self::UNIT_CUBIC_METERS;

    protected array $unitExchanges = [
        self::UNIT_CUBIC_FEMTOMETERS => 1e-45,
        self::UNIT_CUBIC_PICOMETERS => 1e-36,
        self::UNIT_CUBIC_NANOMETERS => 1e-27,
        self::UNIT_CUBIC_MICROMETERS => 1e-18,
        self::UNIT_CUBIC_MILLIMETERS => 1e-9,
        self::UNIT_CUBIC_CENTIMETERS => 1e-6,
        self::UNIT_CUBIC_DECIMETERS => 1e-3,
        self::UNIT_CUBIC_METERS => 1.0,
        self::UNIT_CUBIC_KILOMETERS => 1e9,
        self::UNIT_CUBIC_INCHES => 0.000016387064,
        self::UNIT_CUBIC_FEET => 0.028316846592,
        self::UNIT_CUBIC_YARDS => 0.764554857984,
        self::UNIT_CUBIC_MILES => 4168181825.440579,
        self::UNIT_CUBIC_LITERS => 0.001,
        self::UNIT_CUBIC_GALLONS => 0.003785411784,
        self::UNIT_CUBIC_PINTS => 0.000473176473,
        self::UNIT_CUBIC_QUARTS => 0.000946352946,
    ];

    protected function normalizeBaseUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'fm^3', 'fm³', 'femtometers3', 'femtometers^3', 'femtometers³' => self::UNIT_CUBIC_FEMTOMETERS,
            'pm^3', 'pm³', 'picometers3', 'picometers^3', 'picometers³' => self::UNIT_CUBIC_PICOMETERS,
            'nm^3', 'nm³', 'nanometers3', 'nanometers^3', 'nanometers³' => self::UNIT_CUBIC_NANOMETERS,
            'μm^3', 'μm³', 'um3', 'um^3', 'um³', 'micrometers3', 'micrometers^3', 'micrometers³' => self::UNIT_CUBIC_MICROMETERS,
            'mm^3', 'mm³', 'millimeters3', 'millimeters^3', 'millimeters³' => self::UNIT_CUBIC_MILLIMETERS,
            'cm^3', 'cm³', 'centimeters3', 'centimeters^3', 'centimeters³' => self::UNIT_CUBIC_CENTIMETERS,
            'dm^3', 'dm³', 'decimeters3', 'decimeters^3', 'decimeters³' => self::UNIT_CUBIC_DECIMETERS,
            'm^3', 'm³', 'meters3', 'meters^3', 'meters³' => self::UNIT_CUBIC_METERS,
            'km^3', 'km³', 'kilometers3', 'kilometers^3', 'kilometers³' => self::UNIT_CUBIC_KILOMETERS,
            'in^3', 'in³', 'inches3', 'inches^3', 'inches³' => self::UNIT_CUBIC_INCHES,
            'ft^3', 'ft³', 'feet3', 'feet^3', 'feet³' => self::UNIT_CUBIC_FEET,
            'yd^3', 'yd³', 'yards3', 'yards^3', 'yards³' => self::UNIT_CUBIC_YARDS,
            'mi^3', 'mi³', 'miles3', 'miles^3', 'miles³' => self::UNIT_CUBIC_MILES,
            'liters', 'liter' => self::UNIT_CUBIC_LITERS,
            'gallons', 'gallon' => self::UNIT_CUBIC_GALLONS,
            'pints', 'pint' => self::UNIT_CUBIC_PINTS,
            'quarts', 'quart' => self::UNIT_CUBIC_QUARTS,
            default => $unit,
        };
    }

    public function withOnlyCommonVolumes(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_COMMON_VOLUMES);
    }
}
