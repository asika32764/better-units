# Energy Measurement

Energy 是一個用於計算能量的單位轉換工具，適合用於各種電力、能源、能量單位之間的轉換。

<!-- TOC -->
* [Energy Measurement](#energy-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
    * [限縮單位](#限縮單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
<!-- TOC -->

## 建立

要建立一個 Energy 實例，可以使用以下方法：

```php
use Asika\BetterUnits\Energy;

$energy = new Energy(100); // 100 焦耳
$energy = new Energy(2, Energy::UNIT_KILOJOULE); // 2 千焦耳

$energy = Energy::from(300, 'cal');
$energy = Energy::from('300 cal');
```

## 可用單位

- Atom Unit: `j` (joule)
- Default Unit: `j`
- Base Unit: `j`

| 單位     | 常數                   | 別名                                                 | 比率（相對 j）             | 說明    |
|--------|----------------------|----------------------------------------------------|----------------------|-------|
| `j`    | `UNIT_JOULE`         | `joule`, `joules`                                  | `1`                  | 焦耳    |
| `kj`   | `UNIT_KILOJOULE`     | `kilojoule`, `kilojoules`                          | `1000`               | 千焦耳   |
| `mj`   | `UNIT_MEGAJOULE`     | `megajoule`, `megajoules`                          | `1e6`                | 兆焦耳   |
| `gj`   | `UNIT_GIGAJOULE`     | `gigajoule`, `gigajoules`                          | `1e9`                | 吉焦耳   |
| `tj`   | `UNIT_TERAJOULE`     | `terajoule`, `terajoules`                          | `1e12`               | 太焦耳   |
| `cal`  | `UNIT_CALORIE`       | `calorie`, `calories`                              | `4.184`              | 卡路里   |
| `kcal` | `UNIT_KILOCALORIE`   | `kilocalorie`, `kilocalories`                      | `4184`               | 千卡    |
| `nm`   | `UNIT_NEWTON_METER`  | `newton meter`, `newton meters`                    | `1`                  | 牛頓米   |
| `ev`   | `UNIT_VOLT`          | `electron volt`, `electron volts`, `volt`, `volts` | `1.602176634e-19`    | 電子伏特  |
| `mev`  | `UNIT_MEGAVOLT`      | `megavolt`, `megavolts`                            | `1.602176634e-13`    | 兆電子伏特 |
| `ftlb` | `UNIT_FOOT_POUND`    | `foot pound`, `foot pounds`                        | `1.3558179483314004` | 英尺磅   |
| `wh`   | `UNIT_WATT_HOUR`     | `watt hour`, `watt hours`                          | `3600`               | 瓦時    |
| `kwh`  | `UNIT_KILOWATT_HOUR` | `kilowatt hour`, `kilowatt hours`, `kwhr`          | `3.6e6`              | 千瓦時   |
| `mwh`  | `UNIT_MEGAWATT_HOUR` | `megawatt hour`, `megawatt hours`, `mwhr`          | `3.6e9`              | 兆瓦時   |
| `gwh`  | `UNIT_GIGAWATT_HOUR` | `gigawatt hour`, `gigawatt hours`, `gwhr`          | `3.6e12`             | 吉瓦時   |
| `twh`  | `UNIT_TERAWATT_HOUR` | `terawatt hour`, `terawatt hours`, `twhr`          | `3.6e15`             | 太瓦時   |

### 限縮單位

Energy 類別可使用 `withAvailableUnits()` 方法來限制可用的單位，例如只保留焦耳為基準的單位：

```php
$energy = $energy->withAvailableUnits(
    [
        Energy::UNIT_JOULE,
        Energy::UNIT_KILOJOULE,
        Energy::UNIT_MEGAJOULE,
        Energy::UNIT_GIGAJOULE,
        Energy::UNIT_TERAJOULE,
    ]
);

// OR

$energy = $energy->withOnlyJouleUnits();
```

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Energy 轉換成其他單位的值：

```php
$energy->toJoules();
$energy->toKilojoules(scale: 4);
$energy->to('kcal');
```

支援的函式

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

## 格式化

可以將能量數值格式化成人類可讀的方式：

```php
$energy = \Asika\BetterUnits\Energy::from(12345, 'j')
    ->withOnlyJouleUnits();
echo $energy->humanize(divider: ' and '); // 12kj and 345j
```


