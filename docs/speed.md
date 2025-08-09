# Speed Measurement

`Speed` is an object for converting and calculating speed units. It is suitable for converting between various speed
units.

<!-- TOC -->

* [Speed Measurement](#speed-measurement)
    * [Creation](#creation)
    * [Available Units](#available-units)
        * [Restrict Units](#restrict-units)
    * [Conversion](#conversion)
        * [Dynamic Conversion Methods](#dynamic-conversion-methods)
    * [Formatting](#formatting)

<!-- TOC -->

## Creation

To create a Speed instance, you can use the following methods:

```php
use Asika\BetterUnits\Compound\Speed;

$speed = new Speed(100); // 100 m/s
$speed = new Speed(60, Speed::UNIT_KPH); // 60 km/h

$speed = Speed::from(300, 'mph');
$speed = Speed::from('300 mph');
```

## Available Units

- Atom Unit: `m/s`
- Default Unit: `m/s`
- Base Unit: `m/s`

| Unit    | Constant     | Alias                          | Ratio (relative to m/s) | Description         |
|---------|--------------|--------------------------------|-------------------------|---------------------|
| `m/s`   | `UNIT_MPS`   | `mps`, `meters per second`     | `1`                     | Meters per second   |
| `km/h`  | `UNIT_KPH`   | `kph`, `kilometers per hour`   | `0.2777777778`          | Kilometers per hour |
| `mph`   | `UNIT_MPH`   | `mph`, `miles per hour`        | `0.44704`               | Miles per hour      |
| `knots` | `UNIT_KNOTS` | `knots`, `nautical miles/hour` | `0.514444444`           | Knots               |

> [!note]
> `Speed` is a compound measurement, you can also mix up the units from [Length](./length.md)
> and [Duration](./duration.md) to create speed units, for example: `m/s`, `km/s`, `m/h`, `km/h`, `cm/s`, `cm/h`, etc.
> About Compound Units, please refer to the [Compound Units](../README.md#compound-measurement) documentation.

### Restrict Units

To limit the available units for Speed, you can use the `withAvailableUnits()` method:

```php
$speed = $speed->withAvailableUnits([
    Speed::UNIT_MPS,
    Speed::UNIT_KPH,
]);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Speed to other unit values:

```php
$speed->toMph();
$speed->toKph();
$speed->toKnots(scale: 4);
$speed->to('mph');
```

Supported functions:

- `toMph()`
- `toMps()`
- `toKph()`
- `toKnots()`

### Dynamic Conversion Methods

You can also use `to{numUnit}Per{denoUnit}()` format to convert to any compound units:

```php
$speed->toMetersPerSecond();
$speed->toMilesPerMinutes();
$speed->toKmPerHour(scale: 4); // Also support short unit names
$speed->to('m/s');
```

## Indeterminate Scales

Due to the characteristics of Compound Measurement, an `indeterminateScale` property is maintained within the object to
avoid precision loss during multi-step internal calculations.

For detailed explanations and examples, please refer to
the [Compound Measurement -> Indeterminate Scale](../README.md#indeterminate-scales) documentation.

## Formatting

Speed values can be formatted in a human-readable way:

```php
$speed = \Asika\BetterUnits\Compound\Speed::from(123, 'km/h');
echo $speed->humanize(); // 123km/h

$speed = $speed->convertTo('m/s', scale: 2);
echo $speed->format(); // 34.17m/s
```
