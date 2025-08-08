# Area Measurement

Area 是一個用於計算面積的單位轉換工具，適合用於各種面積單位之間的轉換。

<!-- TOC -->
* [Area Measurement](#area-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
    * [限縮單位](#限縮單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
<!-- TOC -->

## 建立

要建立一個 Area 實例，可以使用以下方法：

```php
use Asika\BetterUnits\Area;

$area = new Area(100); // 100 平方公尺
$area = new Area(2, Area::UNIT_SQUARE_KILOMETERS); // 2 平方公里

$area = Area::from(300, 'cm2');
$area = Area::from('300 cm2');
```

## 可用單位

- Atom Unit: `fm2`
- Default Unit: `m2`
- Base Unit: `m2`

| 單位    | 常數                        | 別名                                            | 比率（相對 m²）        | 說明   |
|-------|---------------------------|-----------------------------------------------|------------------|------|
| `fm2` | `UNIT_SQUARE_FEMTOMETERS` | `fm^2`, `fm²`, `femtometers2`, `femtometers²` | `1e-30`          | 平方飛米 |
| `pm2` | `UNIT_SQUARE_PICOMETERS`  | `pm^2`, `pm²`, `picometers2`, `picometers²`   | `1e-24`          | 平方皮米 |
| `nm2` | `UNIT_SQUARE_NANOMETERS`  | `nm^2`, `nm²`, `nanometers2`, `nanometers²`   | `1e-18`          | 平方奈米 |
| `μm2` | `UNIT_SQUARE_MICROMETERS` | `μm^2`, `um²`, `micrometers2`, `micrometers²` | `1e-12`          | 平方微米 |
| `mm2` | `UNIT_SQUARE_MILLIMETERS` | `mm^2`, `mm²`, `millimeters2`, `millimeters²` | `1e-6`           | 平方毫米 |
| `cm2` | `UNIT_SQUARE_CENTIMETERS` | `cm^2`, `cm²`, `centimeters2`, `centimeters²` | `1e-4`           | 平方公分 |
| `dm2` | `UNIT_SQUARE_DECIMETERS`  | `dm^2`, `dm²`, `decimeters2`, `decimeters²`   | `0.01`           | 平方分米 |
| `m2`  | `UNIT_SQUARE_METERS`      | `m^2`, `m²`, `meters2`, `meters²`             | `1`              | 平方公尺 |
| `km2` | `UNIT_SQUARE_KILOMETERS`  | `km^2`, `km²`, `kilometers2`, `kilometers²`   | `1e6`            | 平方公里 |
| `in2` | `UNIT_SQUARE_INCHES`      | `in^2`, `in²`, `inches2`, `inches²`           | `0.00064516`     | 平方英吋 |
| `ft2` | `UNIT_SQUARE_FEET`        | `ft^2`, `ft²`, `feet2`, `feet²`               | `0.09290304`     | 平方英呎 |
| `yd2` | `UNIT_SQUARE_YARDS`       | `yd^2`, `yd²`, `yards2`, `yards²`             | `0.83612736`     | 平方碼  |
| `mi2` | `UNIT_SQUARE_MILES`       | `mi^2`, `mi²`, `miles2`, `miles²`             | `2589988.110336` | 平方英里 |
| `ac`  | `UNIT_SQUARE_ACRES`       | `ac`, `acre`, `acres`                         | `4046.8564224`   | 英畝   |
| `ha`  | `UNIT_SQUARE_HECTARES`    | `ha`, `hectare`, `hectares`                   | `10000`          | 公頃   |

### 限縮單位

由於面積單位的多樣性，Area 類別提供了 `withOnlyCommonAreas()` 方法來限制可用的單位，僅保留常用的公制面積單位：

```php
$area = $ares->withOnlyCommonAreas();

// OR

$area = $area->withAvailableUnits(Area::UNITS_GROUP_COMMON_AREAS);
```

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Area 轉換成其他單位的值：

```php
$area->toSquareMeters();
$area->toSquareKilometers(scale: 4);
$area->to('ft2');
```

支援的函式

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

## 格式化

可以將面積數值格式化成人類可讀的方式：

```php
$area = \Asika\BetterUnits\Area::from(401074580, 'm2')
    ->withOnlyCommonAreas();
echo $area->humanize(divider: ' and '); // 401km2 and 74580m2
```



