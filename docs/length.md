# Length Measurement

`Length` is an object for converting and calculating length units. It is suitable for converting between various length
units.

<!-- TOC -->
* [Length Measurement](#length-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
<!-- TOC -->

## Creation

To create a Length instance, you can use the following methods:

```php
use Asika\BetterUnits\Length;

$length = new Length(100); // 100 meters
$length = new Length(2, Length::UNIT_KILOMETERS); // 2 kilometers

$length = Length::from(300, 'cm');
$length = Length::from('300 cm');
```

## Available Units

- Atom Unit: `fm`
- Default Unit: `m`
- Base Unit: `m`

| Unit  | Constant                  | Alias                                     | Ratio (relative to m)  | Description       |
|-------|---------------------------|-------------------------------------------|------------------------|-------------------|
| `fm`  | `UNIT_FEMTOMETERS`        | `femtometer`, `femtometers`               | `1e-15`                | Femtometer        |
| `pm`  | `UNIT_PICOMETERS`         | `picometer`, `picometers`                 | `1e-12`                | Picometer         |
| `nm`  | `UNIT_NANOMETERS`         | `nanometer`, `nanometers`                 | `1e-9`                 | Nanometer         |
| `μm`  | `UNIT_MICROMETERS`        | `micrometer`, `micrometers`, `um`         | `1e-6`                 | Micrometer        |
| `mm`  | `UNIT_MILLIMETERS`        | `millimeter`, `millimeters`               | `1e-3`                 | Millimeter        |
| `cm`  | `UNIT_CENTIMETERS`        | `centimeter`, `centimeters`               | `0.01`                 | Centimeter        |
| `dm`  | `UNIT_DECIMETERS`         | `decimeter`, `decimeters`                 | `0.1`                  | Decimeter         |
| `m`   | `UNIT_METERS`             | `meter`, `meters`                         | `1`                    | Meter             |
| `km`  | `UNIT_KILOMETERS`         | `kilometer`, `kilometers`                 | `1000`                 | Kilometer         |
| `in`  | `UNIT_INCHES`             | `inch`, `inches`                          | `0.0254`               | Inch              |
| `ft`  | `UNIT_FEET`               | `foot`, `feet`                            | `0.3048`               | Foot              |
| `yd`  | `UNIT_YARDS`              | `yard`, `yards`                           | `0.9144`               | Yard              |
| `h`   | `UNIT_HANDS`              | `hand`, `hands`                           | `0.1016`               | Hand              |
| `mi`  | `UNIT_MILES`              | `mile`, `miles`                           | `1609.344`             | Mile              |
| `ly`  | `UNIT_LIGHT_YEARS`        | `light year`, `light years`               | `9.461e15`             | Light year        |
| `au`  | `UNIT_ASTRONOMICAL_UNITS` | `astronomical unit`, `astronomical units` | `149597870700.0`       | Astronomical Unit |
| `pc`  | `UNIT_PARSEC`             | `parsec`, `parsecs`                       | `3.085677581491367e16` | Parsec            |
| `fth` | `UNIT_FATHOMS`            | `fathom`, `fathoms`                       | `1.8288`               | Fathom            |
| `nmi` | `UNIT_NAUTICAL_MILES`     | `nautical mile`, `nautical miles`         | `1852`                 | Nautical Mile     |

### Restrict Units

Due to the diversity of length units, the Length class provides the `withOnlyCommonLengths()` method to restrict the
available units, keeping only the commonly used metric length units:

```php
$length = $length->withOnlyCommonLengths();

// OR

$length = $length->withAvailableUnits(Length::UNITS_GROUP_COMMON_LENGTHS);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Length to other unit values:

```php
$length->toMeters();
$length->toKilometers(scale: 4);
$length->to('nmi');
```

Supported functions

- `toFemtometers()`
- `toPicometers()`
- `toNanometers()`
- `toMicrometers()`
- `toMillimeters()`
- `toCentimeters()`
- `toDecimeters()`
- `toMeters()`
- `toKilometers()`
- `toInches()`
- `toFeet()`
- `toYards()`
- `toHands()`
- `toMiles()`
- `toLightYears()`
- `toAstronomicalUnits()`
- `toParsecs()`
- `toFathoms()`
- `toNauticalMiles()`

## Formatting

Length values can be formatted in a human-readable way:

```php
$length = \Asika\BetterUnits\Length::from(12345, 'm')
    ->withOnlyCommonLengths();
echo $length->humanize(divider: ' and '); // 12km and 345m
```
