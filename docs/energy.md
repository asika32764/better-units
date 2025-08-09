# Energy Measurement

`Energy` is an object for converting and calculating energy units. It is useful for converting between different energy,
power, and force units.

<!-- TOC -->
* [Energy Measurement](#energy-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
<!-- TOC -->

## Creation

To create an Energy instance, you can use the following methods:

```php
use Asika\BetterUnits\Energy;

$energy = new Energy(100); // 100 joules
$energy = new Energy(2, Energy::UNIT_KILOJOULE); // 2 kilojoules

$energy = Energy::from(300, 'cal');
$energy = Energy::from('300 cal');
```

## Available Units

- Atom Unit: `j` (joule)
- Default Unit: `j`
- Base Unit: `j`

| Unit   | Constant             | Alias                                              | Ratio (relative to j) | Description        |
|--------|----------------------|----------------------------------------------------|-----------------------|--------------------|
| `j`    | `UNIT_JOULE`         | `joule`, `joules`                                  | `1`                   | Joule              |
| `kj`   | `UNIT_KILOJOULE`     | `kilojoule`, `kilojoules`                          | `1000`                | Kilojoule          |
| `mj`   | `UNIT_MEGAJOULE`     | `megajoule`, `megajoules`                          | `1e6`                 | Megajoule          |
| `gj`   | `UNIT_GIGAJOULE`     | `gigajoule`, `gigajoules`                          | `1e9`                 | Gigajoule          |
| `tj`   | `UNIT_TERAJOULE`     | `terajoule`, `terajoules`                          | `1e12`                | Terajoule          |
| `cal`  | `UNIT_CALORIE`       | `calorie`, `calories`                              | `4.184`               | Calorie            |
| `kcal` | `UNIT_KILOCALORIE`   | `kilocalorie`, `kilocalories`                      | `4184`                | Kilocalorie        |
| `nm`   | `UNIT_NEWTON_METER`  | `newton meter`, `newton meters`                    | `1`                   | Newton Meter       |
| `ev`   | `UNIT_VOLT`          | `electron volt`, `electron volts`, `volt`, `volts` | `1.602176634e-19`     | Electron Volt      |
| `mev`  | `UNIT_MEGAVOLT`      | `megavolt`, `megavolts`                            | `1.602176634e-13`     | Mega Electron Volt |
| `ftlb` | `UNIT_FOOT_POUND`    | `foot pound`, `foot pounds`                        | `1.3558179483314004`  | Foot-Pound         |
| `wh`   | `UNIT_WATT_HOUR`     | `watt hour`, `watt hours`                          | `3600`                | Watt Hour          |
| `kwh`  | `UNIT_KILOWATT_HOUR` | `kilowatt hour`, `kilowatt hours`, `kwhr`          | `3.6e6`               | Kilowatt Hour      |
| `mwh`  | `UNIT_MEGAWATT_HOUR` | `megawatt hour`, `megawatt hours`, `mwhr`          | `3.6e9`               | Megawatt Hour      |
| `gwh`  | `UNIT_GIGAWATT_HOUR` | `gigawatt hour`, `gigawatt hours`, `gwhr`          | `3.6e12`              | Gigawatt Hour      |

### Restrict Units

To limit the available units for Energy, you can use the `withAvailableUnits()` method. For example, to keep only
joule-based units:

```php
$energy = $energy->withAvailableUnits([
    Energy::UNIT_JOULE,
    Energy::UNIT_KILOJOULE,
    Energy::UNIT_MEGAJOULE,
    Energy::UNIT_GIGAJOULE,
    Energy::UNIT_TERAJOULE,
]);

// OR

$energy = $energy->withOnlyJouleUnits();
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Energy to other unit values:

```php
$energy->toJoules();
$energy->toKilojoules(scale: 4);
$energy->to('kcal');
```

Supported functions:

- `toJoules()`
- `toKilojoules()`
- `toMegajoules()`
- `toGigajoules()`
- `toTerajoules()`
- `toCalories()`
- `toKilocalories()`
- `toNewtonMeters()`
- `toVolts()`
- `toMegavolts()`
- `toFootPounds()`
- `toWattHours()`
- `toKilowattHours()`
- `toMegawattHours()`
- `toGigawattHours()`
- `toTerawattHours()`

## Formatting

Energy values can be formatted in a human-readable way:

```php
$energy = \Asika\BetterUnits\Energy::from(12345, 'j')
    ->withOnlyJouleUnits();
echo $energy->humanize(divider: ' and '); // 12kj and 345j
```
