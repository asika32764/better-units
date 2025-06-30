# Length Measurement

Length 是一個用於計算長度的單位轉換工具，適合用於各種長度單位之間的轉換。

<!-- TOC -->
* [Length Measurement](#length-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
    * [限縮單位](#限縮單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
<!-- TOC -->

## 建立

要建立一個 Length 實例，可以使用以下方法：

```php
use Asika\UnitConverter\Length;

$length = new Length(100); // 100 公尺
$length = new Length(2, Length::UNIT_KILOMETERS); // 2 公里

$length = Length::from(300, 'cm');
$length = Length::from('300 cm');
```

## 可用單位

- Atom Unit: `fm`
- Default Unit: `m`
- Base Unit: `m`

| 單位    | 常數                        | 別名                                        | 比率（相對 m）               | 說明   |
|-------|---------------------------|-------------------------------------------|------------------------|------|
| `fm`  | `UNIT_FEMTOMETERS`        | `femtometer`, `femtometers`               | `1e-15`                | 飛米   |
| `pm`  | `UNIT_PICOMETERS`         | `picometer`, `picometers`                 | `1e-12`                | 皮米   |
| `nm`  | `UNIT_NANOMETERS`         | `nanometer`, `nanometers`                 | `1e-9`                 | 奈米   |
| `μm`  | `UNIT_MICROMETERS`        | `micrometer`, `micrometers`, `um`         | `1e-6`                 | 微米   |
| `mm`  | `UNIT_MILLIMETERS`        | `millimeter`, `millimeters`               | `1e-3`                 | 毫米   |
| `cm`  | `UNIT_CENTIMETERS`        | `centimeter`, `centimeters`               | `0.01`                 | 公分   |
| `dm`  | `UNIT_DECIMETERS`         | `decimeter`, `decimeters`                 | `0.1`                  | 分米   |
| `m`   | `UNIT_METERS`             | `meter`, `meters`                         | `1`                    | 公尺   |
| `km`  | `UNIT_KILOMETERS`         | `kilometer`, `kilometers`                 | `1000`                 | 公里   |
| `in`  | `UNIT_INCHES`             | `inch`, `inches`                          | `0.0254`               | 英吋   |
| `ft`  | `UNIT_FEET`               | `foot`, `feet`                            | `0.3048`               | 英呎   |
| `yd`  | `UNIT_YARDS`              | `yard`, `yards`                           | `0.9144`               | 碼    |
| `h`   | `UNIT_HANDS`              | `hand`, `hands`                           | `0.1016`               | 手    |
| `mi`  | `UNIT_MILES`              | `mile`, `miles`                           | `1609.344`             | 英里   |
| `ly`  | `UNIT_LIGHT_YEARS`        | `light year`, `light years`               | `9.461e15`             | 光年   |
| `au`  | `UNIT_ASTRONOMICAL_UNITS` | `astronomical unit`, `astronomical units` | `149597870700.0`       | 天文單位 |
| `pc`  | `UNIT_PARSEC`             | `parsec`, `parsecs`                       | `3.085677581491367e16` | 秒差距  |
| `fth` | `UNIT_FATHOMS`            | `fathom`, `fathoms`                       | `1.8288`               | 噚    |
| `nmi` | `UNIT_NAUTICAL_MILES`     | `nautical mile`, `nautical miles`         | `1852`                 | 海里   |

### 限縮單位

由於長度單位的多樣性，Length 類別提供了 `withOnlyCommonLengths()` 方法來限制可用的單位，僅保留常用的公制長度單位：

```php
$length = $length->withOnlyCommonLengths();

// OR

$length = $length->withAvailableUnits(Length::UNITS_GROUP_COMMON_LENGTHS);
```

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Length 轉換成其他單位的值：

```php
$length->toMeters();
$length->toKilometers(scale: 4);
$length->to('nmi');
```

支援的函式

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

## 格式化

可以將長度數值格式化成人類可讀的方式：

```php
$length = \Asika\UnitConverter\Length::from(12345, 'm')
    ->withOnlyCommonLengths();
echo $length->humanize(divider: ' and '); // 12km and 345m
```


