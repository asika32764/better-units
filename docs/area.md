# Area Measurement

Area is a unit conversion tool for calculating areas, suitable for various area unit conversions.

<!-- TOC -->
* [Area Measurement](#area-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
<!-- TOC -->

## Creation

To create an Area instance, you can use the following methods:

```php
use Asika\BetterUnits\Area;

$area = new Area(100); // 100 square meters
$area = new Area(2, Area::UNIT_SQUARE_KILOMETERS); // 2 square kilometers

$area = Area::from(300, 'cm2');
$area = Area::from('300 cm2');
```

## Available Units

- Atom Unit: `fm2`
- Default Unit: `m2`
- Base Unit: `m2`

| Code  | Constant                  | Aliases                                       | Factor           | Chinese Name      |
|-------|---------------------------|-----------------------------------------------|------------------|-------------------|
| `fm2` | `UNIT_SQUARE_FEMTOMETERS` | `fm^2`, `fm²`, `femtometers2`, `femtometers²` | `1e-30`          | Square Femtometer |
| `pm2` | `UNIT_SQUARE_PICOMETERS`  | `pm^2`, `pm²`, `picometers2`, `picometers²`   | `1e-24`          | Square Picometer  |
| `nm2` | `UNIT_SQUARE_NANOMETERS`  | `nm^2`, `nm²`, `nanometers2`, `nanometers²`   | `1e-18`          | Square Nanometer  |
| `μm2` | `UNIT_SQUARE_MICROMETERS` | `μm^2`, `um²`, `micrometers2`, `micrometers²` | `1e-12`          | Square Micrometer |
| `mm2` | `UNIT_SQUARE_MILLIMETERS` | `mm^2`, `mm²`, `millimeters2`, `millimeters²` | `1e-6`           | Square Millimeter |
| `cm2` | `UNIT_SQUARE_CENTIMETERS` | `cm^2`, `cm²`, `centimeters2`, `centimeters²` | `1e-4`           | Square Centimeter |
| `dm2` | `UNIT_SQUARE_DECIMETERS`  | `dm^2`, `dm²`, `decimeters2`, `decimeters²`   | `0.01`           | Square Decimeter  |
| `m2`  | `UNIT_SQUARE_METERS`      | `m^2`, `m²`, `meters2`, `meters²`             | `1`              | Square Meter      |
| `km2` | `UNIT_SQUARE_KILOMETERS`  | `km^2`, `km²`, `kilometers2`, `kilometers²`   | `1e6`            | Square Kilometer  |
| `in2` | `UNIT_SQUARE_INCHES`      | `in^2`, `in²`, `inches2`, `inches²`           | `0.00064516`     | Square Inch       |
| `ft2` | `UNIT_SQUARE_FEET`        | `ft^2`, `ft²`, `feet2`, `feet²`               | `0.09290304`     | Square Foot       |
| `yd2` | `UNIT_SQUARE_YARDS`       | `yd^2`, `yd²`, `yards2`, `yards²`             | `0.83612736`     | Square Yard       |
| `mi2` | `UNIT_SQUARE_MILES`       | `mi^2`, `mi²`, `miles2`, `miles²`             | `2589988.110336` | Square Mile       |
| `ac`  | `UNIT_SQUARE_ACRES`       | `ac`, `acre`, `acres`                         | `4046.8564224`   | Acre              |
| `ha`  | `UNIT_SQUARE_HECTARES`    | `ha`, `hectare`, `hectares`                   | `10000`          | Hectare           |

### Restrict Units

Because there are many area units, the Area class provides the `withOnlyCommonAreas()` method to limit available units, keeping only the most common metric area units:

```php
$area = $ares->withOnlyCommonAreas();

// OR

$area = $area->withAvailableUnits(Area::UNITS_GROUP_COMMON_AREAS);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Area to other unit values:

```php
$area->toSquareMeters();
$area->toSquareKilometers(scale: 4);
$area->to('ft2');
```

Supported functions

- `toSquareFemtometers()`
- `toSquarePicometers()`
- `toSquareNanometers()`
- `toSquareMicrometers()`
- `toSquareMillimeters()`
- `toSquareCentimeters()`
- `toSquareDecimeters()`
- `toSquareMeters()`
- `toSquareKilometers()`
- `toSquareInches()`
- `toSquareFeet()`
- `toSquareYards()`
- `toSquareMiles()`
- `toSquareAcres()`
- `toSquareHectares()`

## Formatting

Area values can be formatted in a human-readable way:

```php
$area = \Asika\BetterUnits\Area::from(401074580, 'm2')
    ->withOnlyCommonAreas();
echo $area->humanize(divider: ' and '); // 401km2 and 74580m2
```
