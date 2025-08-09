# FileSize Measurement

FileSize is a tool for calculating and converting file size units. It is suitable for displaying, storing, or converting
various byte and bit units.

<!-- TOC -->
* [FileSize Measurement](#filesize-measurement)
  * [Basic Concept: About KB and KiB](#basic-concept-about-kb-and-kib)
  * [Creation](#creation)
  * [Available Units](#available-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
  * [Setup Available Units Quickly](#setup-available-units-quickly)
<!-- TOC -->

## Basic Concept: About KB and KiB

Many packages and online articles often mix up
the [SI standards](https://en.wikipedia.org/wiki/International_System_of_Units) KB (Kilobyte)
and [IEC standards](https://en.wikipedia.org/wiki/International_Electrotechnical_Commission) KiB (Kibibyte), or even
mistakenly write KiB as KB.
This confusion is very common in various documents, tutorials, and code. However, according to international standards ,
KB, MB, GB, and other SI units must be calculated using the decimal system (base 1000), while KiB, MiB,
GiB, and other IEC units must use the binary system (base 1024).

This package strictly follows the standards. All binary units must use KiB, MiB, GiB, etc. Do not use KB to represent
1024 bytes. If you need 1024 bytes, use KiB. If you need 1024 KiB, use MiB. This avoids unit confusion and ensures
accurate calculations.

- SI
    - KB = 1000 bytes
    - MB = 1000 KB = 1,000,000 bytes
    - GB = 1000 MB = 1,000,000,000 bytes
- IEC
    - KiB = 1024 bytes
    - MiB = 1024 KiB = 1,048,576 bytes
    - GiB = 1024 MiB = 1,073,741,824 bytes

Please make sure to choose the correct unit according to the standards so that this package can provide accurate
calculations.

## Creation

To create a FileSize instance, you can use the following methods:

```php
use Asika\BetterUnits\FileSize;

$size = new FileSize(1024); // 1024 bits (default unit)
$size = new FileSize(100, FileSize::UNIT_BYTES); // 100 bytes

$size = FileSize::from(10, 'MiB');
$size = FileSize::from('10MiB');
```

> [!note]
> The units in this Measurement are case-sensitive, as MiB and Mib are two different units.

## Available Units

FileSize supports a variety of units, including bits, bytes, and their SI (decimal) and IEC (binary) variants:

| Unit  | Constant          | Alias (case-insensitive)  | Ratio (relative to bits)          | Description |
|-------|-------------------|---------------------------|-----------------------------------|-------------|
| `b`   | `UNIT_BITS`       | `bit`, `bits`             | 1                                 | Bit         |
| `Kb`  | `UNIT_KILOBITS`   | `kilobit`, `kilobits`     | 1,000                             | Kilobit     |
| `Kib` | `UNIT_KIBIBITS`   | `kibibit`, `kibibits`     | 1,024                             | Kibibit     |
| `Mb`  | `UNIT_MEGABITS`   | `megabit`, `megabits`     | 1,000,000                         | Megabit     |
| `Mib` | `UNIT_MEBIBITS`   | `mebibit`, `mebibits`     | 1,048,576                         | Mebibit     |
| `Gb`  | `UNIT_GIGABITS`   | `gigabit`, `gigabits`     | 1,000,000,000                     | Gigabit     |
| `Gib` | `UNIT_GIBIBITS`   | `gibibit`, `gibibits`     | 1,073,741,824                     | Gibibit     |
| `Tb`  | `UNIT_TERABITS`   | `terabit`, `terabits`     | 1,000,000,000,000                 | Terabit     |
| `Tib` | `UNIT_TEBIBITS`   | `tebibit`, `tebibits`     | 1,099,511,627,776                 | Tebibit     |
| `Pb`  | `UNIT_PETABITS`   | `petabit`, `petabits`     | 1,000,000,000,000,000             | Petabit     |
| `Pib` | `UNIT_PEBIBITS`   | `pebibit`, `pebibits`     | 1,125,899,906,842,624             | Pebibit     |
| `Eb`  | `UNIT_EXABITS`    | `exabit`, `exabits`       | 1,000,000,000,000,000,000         | Exabit      |
| `Eib` | `UNIT_EXBIBITS`   | `exbibit`, `exbibits`     | 1,152,921,504,606,846,976         | Exbibit     |
| `Zb`  | `UNIT_ZETTABITS`  | `zettabit`, `zettabits`   | 1,000,000,000,000,000,000,000     | Zettabit    |
| `Zib` | `UNIT_ZEBIBITS`   | `zebibit`, `zebibits`     | 1,180,591,620,717,411,303,424     | Zebibit     |
| `Yb`  | `UNIT_YOTTABITS`  | `yottabit`, `yottabits`   | 1,000,000,000,000,000,000,000,000 | Yottabit    |
| `Yib` | `UNIT_YOBIBITS`   | `yobibit`, `yobibits`     | 1,208,925,819,614,629,174,706,176 | Yobibit     |
| `B`   | `UNIT_BYTES`      | `byte`, `bytes`           | 8                                 | Byte        |
| `KB`  | `UNIT_KILOBYTES`  | `kilobyte`, `kilobytes`   | 8,000                             | Kilobyte    |
| `KiB` | `UNIT_KIBIBYTES`  | `kibibyte`, `kibibytes`   | 8,192                             | Kibibyte    |
| `MB`  | `UNIT_MEGABYTES`  | `megabyte`, `megabytes`   | 8,000,000                         | Megabyte    |
| `MiB` | `UNIT_MEBIBYTES`  | `mebibyte`, `mebibytes`   | 8,388,608                         | Mebibyte    |
| `GB`  | `UNIT_GIGABYTES`  | `gigabyte`, `gigabytes`   | 8,000,000,000                     | Gigabyte    |
| `GiB` | `UNIT_GIBIBYTES`  | `gibibyte`, `gibibytes`   | 8,589,934,592                     | Gibibyte    |
| `TB`  | `UNIT_TERABYTES`  | `terabyte`, `terabytes`   | 8,000,000,000,000                 | Terabyte    |
| `TiB` | `UNIT_TEBIBYTES`  | `tebibyte`, `tebibytes`   | 8,796,093,022,208                 | Tebibyte    |
| `PB`  | `UNIT_PETABYTES`  | `petabyte`, `petabytes`   | 8,000,000,000,000,000             | Petabyte    |
| `PiB` | `UNIT_PEBIBYTES`  | `pebibyte`, `pebibytes`   | 9,007,199,254,740,992             | Pebibyte    |
| `EB`  | `UNIT_EXABYTES`   | `exabyte`, `exabytes`     | 8,000,000,000,000,000,000         | Exabyte     |
| `EiB` | `UNIT_EXBIBYTES`  | `exbibyte`, `exbibytes`   | 9,223,372,036,854,775,808         | Exbibyte    |
| `ZB`  | `UNIT_ZETTABYTES` | `zettabyte`, `zettabytes` | 8,000,000,000,000,000,000,000     | Zettabyte   |
| `ZiB` | `UNIT_ZEBIBYTES`  | `zebibyte`, `zebibytes`   | 9,444,732,965,739,290,427,392     | Zebibyte    |
| `YB`  | `UNIT_YOTTABYTES` | `yottabyte`, `yottabytes` | 8,000,000,000,000,000,000,000,000 | Yottabyte   |
| `YiB` | `UNIT_YOBIBYTES`  | `yobibyte`, `yobibytes`   | 9,671,406,556,917,033,397,649,408 | Yobibyte    |

## Conversion

You can use the `toXxx()` or `convertTo()` methods to convert FileSize to other unit values:

```php
$size = FileSize::from('10MB');

$size->toBytes(); // 10 * 1,000,000 / 8 = 1,250,000 bytes
$size->toMegabits(); // 80
$size->convertTo(FileSize::UNIT_KIBIBYTES); // 1220.703125 KiB
```

Supported functions (partial list):

- `toBits()`
- `toKilobits()` / `toKibibits()`
- `toMegabits()` / `toMebibits()`
- `toGigabits()` / `toGibibits()`
- `toTerabits()` / `toTebibits()`
- `toPetabits()` / `toPebibits()`
- `toExabits()` / `toExbibits()`
- `toZettabits()` / `toZebibits()`
- `toYottabits()` / `toYobibits()`
- `toBytes()`
- `toKilobytes()` / `toKibibytes()`
- `toMegabytes()` / `toMebibytes()`
- `toGigabytes()` / `toGibibytes()`
- `toTerabytes()` / `toTebibytes()`
- `toPetabytes()` / `toPebibytes()`
- `toExabytes()` / `toExbibytes()`
- `toZettabytes()` / `toZebibytes()`
- `toYottabytes()` / `toYobibytes()`

## Formatting

FileSize provides `format()` and `humanize()` methods to easily convert values into human-readable formats:

```php
$size = FileSize::from('124536KiB')->convertTo(FileSize::UNIT_MEBIBYTES, 3);
echo $size->format(); // 121.617MiB
echo $size->humanize(); // 121MiB 631KiB 827B 3b

$size = FileSize::from('10GiB')->convertTo('MiB');
echo $size->format(); // 10240MiB
echo $size->humanize(); // 10GiB
```

## Setup Available Units Quickly

Quickly switch unit groups, allowing only specific units for formatting and conversion:

```php
$size = new FileSize()->withOnlyBytesBinary(); // Allow only binary bytes units: KiB, MiB, GiB, etc.
$size = new FileSize()->withOnlyBytesBase10(); // Allow only decimal bytes units: KB, MB, GB, etc.
$size = new FileSize()->withOnlyBitsBinary();  // Allow only binary bits units: Kib, Mib, Gib, etc.
$size = new FileSize()->withOnlyBitsBase10();  // Allow only decimal bits units: Kb, Mb, Gb, etc.
```
