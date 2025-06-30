# FileSize Measurement

FileSize 是一個用於計算與轉換檔案大小單位的工具，適合用於顯示、儲存或轉換各種位元組與位元單位。

<!-- TOC -->
* [FileSize Measurement](#filesize-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
  * [單位群組快速設定](#單位群組快速設定)
<!-- TOC -->

## 基本概念，關於 KB 與 KiB

多數套件與網路文章常將 KB（Kilobyte）與 KiB（Kibibyte）混用，甚至將 KiB 誤寫為 KB，這種情況在各種文件、
教學與程式碼中非常普遍。然而，根據國際標準（IEC 80000-13），KB、MB、GB 等 SI 單位必須以 10 進位（1000 為基數）計算，
而 KiB、MiB、GiB 等 IEC 單位則以 2 進位（1024 為基數）計算。

本套件嚴格遵循標準，所有二進位單位必須使用 KiB、MiB、GiB 等標記，請勿將 KB 當作 1024 bytes 使用。
若需 1024 bytes，請使用 KiB，若需 1024 KiB，請使用 MiB。如此可避免單位混淆，確保計算正確。

- KB = 1000 bytes
- KiB = 1024 bytes
- MB = 1000 KB = 1,000,000 bytes
- MiB = 1024 KiB = 1,048,576 bytes

請務必依照標準選用正確單位，本套件才能提供正確的計算。

## 建立

要建立一個 FileSize 實例，可以使用以下方法：

```php
use Asika\UnitConverter\FileSize;

$size = new FileSize(1024); // 1024 bits (預設單位)
$size = new FileSize(100, FileSize::UNIT_BYTES); // 100 bytes

$size = FileSize::from(10, 'MiB');
$size = FileSize::from('10MiB');
```

> [!note]
> 本 Measurement 的單位是大小寫敏感的，因為 MiB 與 Mib 是兩個不同的單位。

## 可用單位

FileSize 支援多種單位，包含 bits、bytes 及其 SI（十進位）與 IEC（二進位）變體：

| 單位         | 常數                    | 別名（不分大小寫）                | 比率（相對 bits） | 說明         |
|--------------|-------------------------|-----------------------------------|------------------|--------------|
| `b`          | `UNIT_BITS`             | `bit`, `bits`                     | 1                | 位元         |
| `Kb`         | `UNIT_KILOBITS`         | `kilobit`, `kilobits`             | 1,000            | 千位元       |
| `Kib`        | `UNIT_KIBIBITS`         | `kibibit`, `kibibits`             | 1,024            | 千二十四位元 |
| `Mb`         | `UNIT_MEGABITS`         | `megabit`, `megabits`             | 1,000,000        | 兆位元       |
| `Mib`        | `UNIT_MEBIBITS`         | `mebibit`, `mebibits`             | 1,048,576        | 兆二十四位元 |
| `Gb`         | `UNIT_GIGABITS`         | `gigabit`, `gigabits`             | 1,000,000,000    | 吉位元       |
| `Gib`        | `UNIT_GIBIBITS`         | `gibibit`, `gibibits`             | 1,073,741,824    | 吉二十四位元 |
| `Tb`         | `UNIT_TERABITS`         | `terabit`, `terabits`             | 1,000,000,000,000 | 太位元      |
| `Tib`        | `UNIT_TEBIBITS`         | `tebibit`, `tebibits`             | 1,099,511,627,776 | 太二十四位元 |
| `Pb`         | `UNIT_PETABITS`         | `petabit`, `petabits`             | 1,000,000,000,000,000 | 拍位元   |
| `Pib`        | `UNIT_PEBIBITS`         | `pebibit`, `pebibits`             | 1,125,899,906,842,624 | 拍二十四位元 |
| `Eb`         | `UNIT_EXABITS`          | `exabit`, `exabits`               | 1,000,000,000,000,000,000 | 艾位元 |
| `Eib`        | `UNIT_EXBIBITS`         | `exbibit`, `exbibits`             | 1,152,921,504,606,846,976 | 艾二十四位元 |
| `Zb`         | `UNIT_ZETTABITS`        | `zettabit`, `zettabits`           | 1,000,000,000,000,000,000,000 | 泽位元 |
| `Zib`        | `UNIT_ZEBIBITS`         | `zebibit`, `zebibits`             | 1,180,591,620,717,411,303,424 | 泽二十四位元 |
| `Yb`         | `UNIT_YOTTABITS`        | `yottabit`, `yottabits`           | 1,000,000,000,000,000,000,000,000 | 尧位元 |
| `Yib`        | `UNIT_YOBIBITS`         | `yobibit`, `yobibits`             | 1,208,925,819,614,629,174,706,176 | 尧二十四位元 |
| `B`          | `UNIT_BYTES`            | `byte`, `bytes`                   | 8                | 位元組       |
| `KB`         | `UNIT_KILOBYTES`        | `kilobyte`, `kilobytes`           | 8,000            | 千位元組     |
| `KiB`        | `UNIT_KIBIBYTES`        | `kibibyte`, `kibibytes`           | 8,192            | 千二十四位元組 |
| `MB`         | `UNIT_MEGABYTES`        | `megabyte`, `megabytes`           | 8,000,000        | 兆位元組     |
| `MiB`        | `UNIT_MEBIBYTES`        | `mebibyte`, `mebibytes`           | 8,388,608        | 兆二十四位元組 |
| `GB`         | `UNIT_GIGABYTES`        | `gigabyte`, `gigabytes`           | 8,000,000,000    | 吉位元組     |
| `GiB`        | `UNIT_GIBIBYTES`        | `gibibyte`, `gibibytes`           | 8,589,934,592    | 吉二十四位元組 |
| `TB`         | `UNIT_TERABYTES`        | `terabyte`, `terabytes`           | 8,000,000,000,000 | 太位元組    |
| `TiB`        | `UNIT_TEBIBYTES`        | `tebibyte`, `tebibytes`           | 8,796,093,022,208 | 太二十四位元組 |
| `PB`         | `UNIT_PETABYTES`        | `petabyte`, `petabytes`           | 8,000,000,000,000,000 | 拍位元組 |
| `PiB`        | `UNIT_PEBIBYTES`        | `pebibyte`, `pebibytes`           | 9,007,199,254,740,992 | 拍二十四位元組 |
| `EB`         | `UNIT_EXABYTES`         | `exabyte`, `exabytes`             | 8,000,000,000,000,000,000 | 艾位元組 |
| `EiB`        | `UNIT_EXBIBYTES`        | `exbibyte`, `exbibytes`           | 9,223,372,036,854,775,808 | 艾二十四位元組 |
| `ZB`         | `UNIT_ZETTABYTES`       | `zettabyte`, `zettabytes`         | 8,000,000,000,000,000,000,000 | 泽位元組 |
| `ZiB`        | `UNIT_ZEBIBYTES`        | `zebibyte`, `zebibytes`           | 9,444,732,965,739,290,427,392 | 泽二十四位元組 |
| `YB`         | `UNIT_YOTTABYTES`       | `yottabyte`, `yottabytes`         | 8,000,000,000,000,000,000,000,000 | 尧位元組 |
| `YiB`        | `UNIT_YOBIBYTES`        | `yobibyte`, `yobibytes`           | 9,671,406,556,917,033,397,649,408 | 尧二十四位元組 |

## 轉換

可使用 `toXxx()` 或 `convertTo()` 方法將 FileSize 轉換成其他單位的值：

```php
$size = FileSize::from('10MB');

$size->toBytes(); // 10 * 1,000,000 / 8 = 1,250,000 bytes
$size->toMegabits(); // 80
$size->convertTo(FileSize::UNIT_KIBIBYTES); // 1220.703125 KiB
```

支援的函式（部分）：

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

## 格式化

FileSize 提供 `format()` 與 `humanize()` 方法，方便將數值轉為人類可讀的格式：

```php
$size = FileSize::from('124536KiB')->convertTo(FileSize::UNIT_MEBIBYTES, 3);
echo $size->format(); // 121.617MiB
echo $size->humanize(); // 121MiB 631KiB 827B 3b

$size = FileSize::from('10GiB')->convertTo('MiB');
echo $size->format(); // 10240MiB
echo $size->humanize(); // 10GiB
```

## 單位群組快速設定

可快速切換單位群組，僅允許特定單位進行格式化與轉換：

```php
$size = new FileSize()->withOnlyBytesBinary(); // 只允許二進位 bytes 單位，KiB、MiB、GiB 等
$size = new FileSize()->withOnlyBytesBase10(); // 只允許十進位 bytes 單位，KB、MB、GB 等
$size = new FileSize()->withOnlyBitsBinary();  // 只允許二進位 bits 單位，Kib、Mib、Gib 等
$size = new FileSize()->withOnlyBitsBase10();  // 只允許十進位 bits 單位，Kb、Mb、Gb 等
```
