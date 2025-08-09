# Bitrate Measurement

`Bitrate` is an object for converting and calculating bitrate units. It is suitable for converting between various
bitrate units, such as bits per second, bytes per second, and their multiples.

<!-- TOC -->
* [Bitrate Measurement](#bitrate-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
    * [Dynamic Conversion Methods](#dynamic-conversion-methods)
  * [Indeterminate Scales](#indeterminate-scales)
  * [Formatting](#formatting)
<!-- TOC -->

## Creation

To create a Bitrate instance, you can use the following methods:

```php
use Asika\BetterUnits\Compound\Bitrate;

$bitrate = new Bitrate(100); // 100 bits/s
$bitrate = new Bitrate(1, Bitrate::UNIT_MBPS); // 1 Mbps

$bitrate = Bitrate::from(300, 'Kbps');
$bitrate = Bitrate::from('300 Kbps');
```

## Available Units

- Atom Unit: `bits/s`
- Default Unit: `bytes/s`
- Base Unit: `bits/s`

| Unit      | Constant                | Alias                     | Ratio (relative to bits/s) | Description         |
|-----------|-------------------------|---------------------------|----------------------------|---------------------|
| `bits/s`  | `UNIT_BITS_PER_SECOND`  | `bps`, `bits per second`  | `1`                        | Bits per second     |
| `bytes/s` | `UNIT_BYTES_PER_SECOND` | `Bps`, `bytes per second` | `8`                        | Bytes per second    |
| `Kbps`    | `UNIT_KBPS`             | `kilobits per second`     | `1000`                     | Kilobits per second |
| `Mbps`    | `UNIT_MBPS`             | `megabits per second`     | `1000000`                  | Megabits per second |
| `Gbps`    | `UNIT_GBPS`             | `gigabits per second`     | `1000000000`               | Gigabits per second |
| `Tbps`    | `UNIT_TBPS`             | `terabits per second`     | `1000000000000`            | Terabits per second |
| `Kibps`   | `UNIT_KIBPS`            | `kibibits per second`     | `1024`                     | Kibibits per second |
| `Mibps`   | `UNIT_MIBPS`            | `mebibits per second`     | `1048576`                  | Mebibits per second |
| `Gibps`   | `UNIT_GIBPS`            | `gibibits per second`     | `1073741824`               | Gibibits per second |
| `Tibps`   | `UNIT_TIBPS`            | `tebibits per second`     | `1099511627776`            | Tebibits per second |

> [!note]
> `Speed` is a compound measurement, you can also mix up the units from [FileSize](./filesize.md)
> and [Duration](./duration.md) to create speed units, for example: `bits/s`, `bytes/s`, `Kbps`, `Mbps`, `Gbps`, 
> `Tbps`, `Kibps`, `Mibps`, `Gibps`, `Tibps`, etc.
> About Compound Units, please refer to the [Compound Units](../README.md#compound-measurement) documentation.

### Restrict Units

To limit the available units for Bitrate, you can use the `withAvailableUnits()` method:

```php
$bitrate = $bitrate->withAvailableUnits([
    Bitrate::UNIT_MBPS,
    Bitrate::UNIT_KBPS,
]);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Bitrate to other unit values:

```php
$bitrate->toMbps();
$bitrate->toKibps(scale: 4);
$bitrate->toKbps(scale: 4);
$bitrate->to('Gbps');
```

Supported functions:

- `toBitsPerSecond()`
- `toBytesPerSecond()`
- `toKbps()`
- `toMbps()`
- `toGbps()`
- `toTbps()`
- `toKibps()`
- `toMibps()`
- `toGibps()`
- `toTibps()`

### Dynamic Conversion Methods

You can also use `to{numUnit}Per{denoUnit}()` format to convert to any compound units:

```php
$bitrate->toBitsPerSecond();
$bitrate->toBytesPerSecond();
$bitrate->toKibibitsPerSecond(scale: 4);
$bitrate->toKibPerSecond(scale: 4); // Also support short unit names
$bitrate->to('bytes/s');
```

## Indeterminate Scales

Due to the characteristics of Compound Measurement, an `indeterminateScale` property is maintained within the object to
avoid precision loss during multi-step internal calculations.

For detailed explanations and examples, please refer to
the [Compound Measurement -> Indeterminate Scale](../README.md#indeterminate-scales) documentation.

## Formatting

Bitrate values can be formatted in a human-readable way:

```php
$bitrate = \Asika\BetterUnits\Compound\Bitrate::from(123, 'Mbps');
echo $bitrate->humanize(); // 123Mbps

$bitrate = $bitrate->convertTo('Kbps', scale: 2);
echo $bitrate->format(); // 123000.00Kbps
```
