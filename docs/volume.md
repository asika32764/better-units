# Volume Measurement

`Volume` is an object for converting and calculating volume units. It is suitable for converting between various volume
units.

<!-- TOC -->
* [Volume Measurement](#volume-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
<!-- TOC -->

## Creation

To create a Volume instance, you can use the following methods:

```php
use Asika\BetterUnits\Volume;

$volume = new Volume(100); // 100 cubic meters
$volume = new Volume(2, Volume::UNIT_CUBIC_KILOMETERS); // 2 cubic kilometers

$volume = Volume::from(300, 'cm3');
$volume = Volume::from('300 cm3');
```

## Available Units

- Atom Unit: `fm3`
- Default Unit: `m3`
- Base Unit: `m3`

| Unit  | Constant                 | Alias                                         | Ratio (relative to m³) | Description      |
|-------|--------------------------|-----------------------------------------------|------------------------|------------------|
| `fm3` | `UNIT_CUBIC_FEMTOMETERS` | `fm^3`, `fm³`, `femtometers3`, `femtometers³` | `1e-45`                | Cubic Femtometer |
| `pm3` | `UNIT_CUBIC_PICOMETERS`  | `pm^3`, `pm³`, `picometers3`, `picometers³`   | `1e-36`                | Cubic Picometer  |
| `nm3` | `UNIT_CUBIC_NANOMETERS`  | `nm^3`, `nm³`, `nanometers3`, `nanometers³`   | `1e-27`                | Cubic Nanometer  |
| `μm3` | `UNIT_CUBIC_MICROMETERS` | `μm^3`, `um³`, `micrometers3`, `micrometers³` | `1e-18`                | Cubic Micrometer |
| `mm3` | `UNIT_CUBIC_MILLIMETERS` | `mm^3`, `mm³`, `millimeters3`, `millimeters³` | `1e-9`                 | Cubic Millimeter |
| `cm3` | `UNIT_CUBIC_CENTIMETERS` | `cm^3`, `cm³`, `centimeters3`, `centimeters³` | `1e-6`                 | Cubic Centimeter |
| `dm3` | `UNIT_CUBIC_DECIMETERS`  | `dm^3`, `dm³`, `decimeters3`, `decimeters³`   | `1e-3`                 | Cubic Decimeter  |
| `m3`  | `UNIT_CUBIC_METERS`      | `m^3`, `m³`, `meters3`, `meters³`             | `1`                    | Cubic Meter      |
| `km3` | `UNIT_CUBIC_KILOMETERS`  | `km^3`, `km³`, `kilometers3`, `kilometers³`   | `1e9`                  | Cubic Kilometer  |
| `in3` | `UNIT_CUBIC_INCHES`      | `in^3`, `in³`, `inches3`, `inches³`           | `0.000016387064`       | Cubic Inch       |
| `ft3` | `UNIT_CUBIC_FEET`        | `ft^3`, `ft³`, `feet3`, `feet³`               | `0.028316846592`       | Cubic Foot       |
| `yd3` | `UNIT_CUBIC_YARDS`       | `yd^3`, `yd³`, `yards3`, `yards³`             | `0.764554857984`       | Cubic Yard       |
| `mi3` | `UNIT_CUBIC_MILES`       | `mi^3`, `mi³`, `miles3`, `miles³`             | `4168181825.440579`    | Cubic Mile       |
| `L`   | `UNIT_CUBIC_LITERS`      | `l`, `liters`, `liter`                        | `0.001`                | Liter            |
| `gal` | `UNIT_CUBIC_GALLONS`     | `gal`, `gallons`, `gallon`                    | `0.003785411784`       | Gallon           |
| `pt`  | `UNIT_CUBIC_PINTS`       | `pt`, `pints`, `pint`                         | `0.000473176473`       | Pint             |
| `qt`  | `UNIT_CUBIC_QUARTS`      | `qt`, `quarts`, `quart`                       | `0.000946352946`       | Quart            |

### Restrict Units

Due to the diversity of volume units, the Volume class provides the `withOnlyCommonVolumes()` method to restrict the
available units, keeping only the commonly used metric volume units:

```php
$volume = $volume->withOnlyCommonVolumes();

// OR

$volume = $volume->withAvailableUnits(Volume::UNITS_GROUP_COMMON_VOLUMES);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert the Volume to other unit values:

```php
$volume->toCubicMeters();
$volume->toCubicKilometers(scale: 4);
$volume->to('ft3');
```

Supported functions:

- `toCubicFemtometers()`
- `toCubicPicometers()`
- `toCubicNanometers()`
- `toCubicMicrometers()`
- `toCubicMillimeters()`
- `toCubicCentimeters()`
- `toCubicDecimeters()`
- `toCubicMeters()`
- `toCubicKilometers()`
- `toCubicInches()`
- `toCubicFeet()`
- `toCubicYards()`
- `toCubicMiles()`
- `toCubicLiters()`
- `toCubicGallons()`
- `toCubicPints()`
- `toCubicQuarts()`

## Formatting

Volume values can be formatted in a human-readable way:

```php
$volume = \Asika\BetterUnits\Volume::from(401074580, 'm3')
    ->withOnlyCommonVolumes();
echo $volume->humanize(divider: ' and '); // 401km3 and 74580m3
```
