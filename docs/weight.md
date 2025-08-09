# Weight Measurement

`Weight` is an object for converting and calculating weight units. It is suitable for converting between various weight
units.

<!-- TOC -->
* [Weight Measurement](#weight-measurement)
  * [Creation](#creation)
  * [Available Units](#available-units)
    * [Restrict Units](#restrict-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
  * [Gravity Acceleration](#gravity-acceleration)
<!-- TOC -->

## Creation

To create a Weight instance, you can use the following methods:

```php
use Asika\BetterUnits\Weight;

$weight = new Weight(100); // 100 grams
$weight = new Weight(2, Weight::UNIT_KILOGRAMS); // 2 kilograms

$weight = Weight::from(300, 'mg');
$weight = Weight::from('300 mg');
```

## Available Units

- Atom Unit: `fg`
- Default Unit: `g`
- Base Unit: `g`

| Unit | Constant           | Alias                                          | Ratio (relative to g) | Description       |
|------|--------------------|------------------------------------------------|-----------------------|-------------------|
| `fg` | `UNIT_FEMTOGRAMS`  | `femtogram`, `femtograms`                      | `1e-15`               | Femtogram         |
| `pg` | `UNIT_PICOGRAMS`   | `picogram`, `picograms`                        | `1e-12`               | Picogram          |
| `ng` | `UNIT_NANOGRAMS`   | `nanogram`, `nanograms`                        | `1e-9`                | Nanogram          |
| `μg` | `UNIT_MICROGRAMS`  | `microgram`, `micrograms`, `mcg`               | `1e-6`                | Microgram         |
| `mg` | `UNIT_MILLIGRAMS`  | `milligram`, `milligrams`                      | `1e-3`                | Milligram         |
| `g`  | `UNIT_GRAMS`       | `gram`, `grams`                                | `1`                   | Gram              |
| `dg` | `UNIT_DECIGRAMS`   | `decigram`, `decigrams`                        | `0.1`                 | Decigram          |
| `cg` | `UNIT_CENTIGRAMS`  | `centigram`, `centigrams`                      | `0.01`                | Centigram         |
| `kg` | `UNIT_KILOGRAMS`   | `kilogram`, `kilograms`                        | `1000`                | Kilogram          |
| `t`  | `UNIT_METRIC_TONS` | `metric ton`, `metric tons`, `tonne`, `tonnes` | `1e6`                 | Metric Ton        |
| `oz` | `UNIT_OUNCES`      | `ounce`, `ounces`                              | `28.349523125`        | Ounce             |
| `lb` | `UNIT_POUNDS`      | `pound`, `pounds`                              | `453.59237`           | Pound             |
| `st` | `UNIT_STONES`      | `stone`, `stones`                              | `6350.29318`          | Stone             |
| `tn` | `UNIT_TONS`        | `ton`, `tons`                                  | `907184.74`           | Ton               |
| `ct` | `UNIT_CARATS`      | `carat`, `carats`                              | `0.2`                 | Carat             |
| `N`  | `UNIT_NEWTONS`     | `newton`, `newtons`                            | `101.972`             | Newton (variable) |

### Restrict Units

Due to the diversity of weight units, the Weight class provides the `withOnlyCommonWeights()` method to restrict the
available units, keeping only the commonly used metric weight units:

```php
$weight = $weight->withOnlyCommonWeights();

// OR

$weight = $weight->withAvailableUnits(Weight::UNITS_GROUP_COMMON_WEIGHTS);
```

## Conversion

You can use the `to()` or `toXxx()` methods to convert Weight to other unit values:

```php
$weight->toGrams();
$weight->toKilograms(scale: 4);
$weight->to('lb');
```

Supported functions

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

## Formatting

Weight values can be formatted in a human-readable way:

```php
$weight = \Asika\BetterUnits\Weight::from(12345, 'g')
    ->withOnlyCommonWeights();
echo $weight->humanize(divider: ' and '); // 12kg and 345g
```

## Gravity Acceleration

Since the value of Newton (`N`) is based on gravity acceleration, we can set the gravity acceleration value for the
`Weight` instance:

```php
$weight = new \Asika\BetterUnits\Weight(100, 'N');

echo $weight->format(unit: 'kg', scale: 4); // 0.0102kg

// The gravity acceleration on the Moon
$weight = $weight->withGravityAcceleration(1.62);

echo $weight->format(unit: 'kg', scale: 4); // 0.0617kg
```
