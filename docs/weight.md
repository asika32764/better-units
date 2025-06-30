# Weight Measurement

Weight 是一個用於計算重量的單位轉換工具，適合用於各種重量單位之間的轉換。

<!-- TOC -->
* [Weight Measurement](#weight-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
    * [限縮單位](#限縮單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
<!-- TOC -->

## 建立

要建立一個 Weight 實例，可以使用以下方法：

```php
use Asika\UnitConverter\Weight;

$weight = new Weight(100); // 100 公克
$weight = new Weight(2, Weight::UNIT_KILOGRAMS); // 2 公斤

$weight = Weight::from(300, 'mg');
$weight = Weight::from('300 mg');
```

## 可用單位

- Atom Unit: `fg`
- Default Unit: `g`
- Base Unit: `g`

| 單位    | 常數                        | 別名                                        | 比率（相對 g）               | 說明       |
|-------|---------------------------|-------------------------------------------|------------------------|----------|
| `fg`  | `UNIT_FEMTOGRAMS`         | `femtogram`, `femtograms`                 | `1e-15`                | 飛克       |
| `pg`  | `UNIT_PICOGRAMS`          | `picogram`, `picograms`                   | `1e-12`                | 皮克       |
| `ng`  | `UNIT_NANOGRAMS`          | `nanogram`, `nanograms`                   | `1e-9`                 | 奈克       |
| `μg`  | `UNIT_MICROGRAMS`         | `microgram`, `micrograms`, `mcg`          | `1e-6`                 | 微克       |
| `mg`  | `UNIT_MILLIGRAMS`         | `milligram`, `milligrams`                 | `1e-3`                 | 毫克       |
| `g`   | `UNIT_GRAMS`              | `gram`, `grams`                           | `1`                    | 公克       |
| `dg`  | `UNIT_DECIGRAMS`          | `decigram`, `decigrams`                   | `0.1`                  | 分克       |
| `cg`  | `UNIT_CENTIGRAMS`         | `centigram`, `centigrams`                 | `0.01`                 | 釐克       |
| `kg`  | `UNIT_KILOGRAMS`          | `kilogram`, `kilograms`                   | `1000`                 | 公斤       |
| `t`   | `UNIT_METRIC_TONS`        | `metric ton`, `metric tons`, `tonne`, `tonnes` | `1e6`             | 公噸       |
| `oz`  | `UNIT_OUNCES`             | `ounce`, `ounces`                         | `28.349523125`         | 盎司       |
| `lb`  | `UNIT_POUNDS`             | `pound`, `pounds`                         | `453.59237`            | 磅        |
| `st`  | `UNIT_STONES`             | `stone`, `stones`                         | `6350.29318`           | 英石       |
| `tn`  | `UNIT_TONS`               | `ton`, `tons`                             | `907184.74`            | 美噸       |
| `ct`  | `UNIT_CARATS`             | `carat`, `carats`                         | `0.2`                  | 克拉       |
| `N`   | `UNIT_NEWTONS`            | `newton`, `newtons`                       | `101.972`              | 牛頓 （可變動） |

### 限縮單位

由於重量單位的多樣性，Weight 類別提供了 `withOnlyCommonWeights()` 方法來限制可用的單位，僅保留常用的公制重量單位：

```php
$weight = $weight->withOnlyCommonWeights();

// OR

$weight = $weight->withAvailableUnits(Weight::UNITS_GROUP_COMMON_WEIGHTS);
```

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Weight 轉換成其他單位的值：

```php
$weight->toGrams();
$weight->toKilograms(scale: 4);
$weight->to('lb');
```

支援的函式

- `toFemtograms()`
- `toPicograms()`
- `toNanograms()`
- `toMicrograms()`
- `toMilligrams()`
- `toGrams()`
- `toDecigrams()`
- `toCentigrams()`
- `toKilograms()`
- `toMetricTons()`
- `toOunces()`
- `toPounds()`
- `toStones()`
- `toTons()`
- `toCarats()`
- `toNewtons()`

## 格式化

可以將重量數值格式化成人類可讀的方式：

```php
$weight = \Asika\UnitConverter\Weight::from(12345, 'g')
    ->withOnlyCommonWeights();
echo $weight->humanize(divider: ' and '); // 12kg and 345g
```

## 重力加速度

由於牛頓 (`N`) 的數值建立在重力加速度上，我們可以替 Weight 實例設定重力加速度的值：

```php
$weight = new \Asika\UnitConverter\Weight(100, 'N');

echo $weight->format(unit: 'kg', scale: 4); // 0.0102kg

$weight = $weight->withGravityAcceleration(1.62); // The gravity acceleration on the Moon

echo $weight->format(unit: 'kg', scale: 4); // 0.0617kg
```
