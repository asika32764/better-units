# Volume Measurement

Volume 是一個用於計算體積的單位轉換工具，適合用於各種體積單位之間的轉換。

<!-- TOC -->
* [Volume Measurement](#volume-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
    * [限縮單位](#限縮單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
<!-- TOC -->

## 建立

要建立一個 Volume 實例，可以使用以下方法：

```php
use Asika\BetterUnits\Volume;

$volume = new Volume(100); // 100 立方公尺
$volume = new Volume(2, Volume::UNIT_CUBIC_KILOMETERS); // 2 立方公里

$volume = Volume::from(300, 'cm3');
$volume = Volume::from('300 cm3');
```

## 可用單位

- Atom Unit: `fm3`
- Default Unit: `m3`
- Base Unit: `m3`

| 單位    | 常數                       | 別名                                            | 比率（相對 m³）           | 說明   |
|-------|--------------------------|-----------------------------------------------|---------------------|------|
| `fm3` | `UNIT_CUBIC_FEMTOMETERS` | `fm^3`, `fm³`, `femtometers3`, `femtometers³` | `1e-45`             | 立方飛米 |
| `pm3` | `UNIT_CUBIC_PICOMETERS`  | `pm^3`, `pm³`, `picometers3`, `picometers³`   | `1e-36`             | 立方皮米 |
| `nm3` | `UNIT_CUBIC_NANOMETERS`  | `nm^3`, `nm³`, `nanometers3`, `nanometers³`   | `1e-27`             | 立方奈米 |
| `μm3` | `UNIT_CUBIC_MICROMETERS` | `μm^3`, `um³`, `micrometers3`, `micrometers³` | `1e-18`             | 立方微米 |
| `mm3` | `UNIT_CUBIC_MILLIMETERS` | `mm^3`, `mm³`, `millimeters3`, `millimeters³` | `1e-9`              | 立方毫米 |
| `cm3` | `UNIT_CUBIC_CENTIMETERS` | `cm^3`, `cm³`, `centimeters3`, `centimeters³` | `1e-6`              | 立方公分 |
| `dm3` | `UNIT_CUBIC_DECIMETERS`  | `dm^3`, `dm³`, `decimeters3`, `decimeters³`   | `1e-3`              | 立方分米 |
| `m3`  | `UNIT_CUBIC_METERS`      | `m^3`, `m³`, `meters3`, `meters³`             | `1`                 | 立方公尺 |
| `km3` | `UNIT_CUBIC_KILOMETERS`  | `km^3`, `km³`, `kilometers3`, `kilometers³`   | `1e9`               | 立方公里 |
| `in3` | `UNIT_CUBIC_INCHES`      | `in^3`, `in³`, `inches3`, `inches³`           | `0.000016387064`    | 立方英吋 |
| `ft3` | `UNIT_CUBIC_FEET`        | `ft^3`, `ft³`, `feet3`, `feet³`               | `0.028316846592`    | 立方英呎 |
| `yd3` | `UNIT_CUBIC_YARDS`       | `yd^3`, `yd³`, `yards3`, `yards³`             | `0.764554857984`    | 立方碼  |
| `mi3` | `UNIT_CUBIC_MILES`       | `mi^3`, `mi³`, `miles3`, `miles³`             | `4168181825.440579` | 立方英里 |
| `L`   | `UNIT_CUBIC_LITERS`      | `l`, `liters`, `liter`                        | `0.001`             | 公升   |
| `gal` | `UNIT_CUBIC_GALLONS`     | `gal`, `gallons`, `gallon`                    | `0.003785411784`    | 加侖   |
| `pt`  | `UNIT_CUBIC_PINTS`       | `pt`, `pints`, `pint`                         | `0.000473176473`    | 品脫   |
| `qt`  | `UNIT_CUBIC_QUARTS`      | `qt`, `quarts`, `quart`                       | `0.000946352946`    | 夸脫   |

### 限縮單位

由於體積單位的多樣性，Volume 類別提供了 `withOnlyCommonVolumes()` 方法來限制可用的單位，僅保留常用的公制體積單位：

```php
$volume = $volume->withOnlyCommonVolumes();

// OR

$volume = $volume->withAvailableUnits(Volume::UNITS_GROUP_COMMON_VOLUMES);
```

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Volume 轉換成其他單位的值：

```php
$volume->toCubicMeters();
$volume->toCubicKilometers(scale: 4);
$volume->to('ft3');
```

支援的函式

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

## 格式化

可以將體積數值格式化成人類可讀的方式：

```php
$volume = \Asika\BetterUnits\Volume::from(401074580, 'm3')
    ->withOnlyCommonVolumes();
echo $volume->humanize(divider: ' and '); // 401km3 and 74580m3
```


