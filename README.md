# BetterUnits - A Better PHP Measurement Converter

BetterUnits is a modern and intuitive unit conversion tool that allows you to convert between various
units of measurement. It supports a wide range of categories including length, weight, temperature, volume, and more.

<!-- TOC -->
* [BetterUnits - A Better PHP Measurement Converter](#betterunits---a-better-php-measurement-converter)
  * [Installation](#installation)
    * [Requirements](#requirements)
    * [Install via Composer](#install-via-composer)
  * [Getting Started](#getting-started)
    * [How to Use This Package](#how-to-use-this-package)
    * [How to Create Measurement Object](#how-to-create-measurement-object)
    * [Rounding Mode [!important]](#rounding-mode-important)
    * [Create By Strings](#create-by-strings)
  * [Unit Conversion](#unit-conversion)
    * [Output Values](#output-values)
    * [convertTo() Method](#convertto-method)
    * [Precision Control](#precision-control)
  * [Units](#units)
  * [Formatting](#formatting)
    * [`format()`](#format)
    * [`humanize()`](#humanize)
    * [Default Formatting Handler](#default-formatting-handler)
    * [`serialize()`](#serialize)
    * [`serializeCallback()`](#serializecallback)
  * [Unit Management](#unit-management)
    * [Restrict Available Units](#restrict-available-units)
    * [Customizing or Adding Units](#customizing-or-adding-units)
    * [Changing Conversion Rates](#changing-conversion-rates)
    * [Other Unit Adjustments](#other-unit-adjustments)
  * [Get the Unit Closest to 1](#get-the-unit-closest-to-1)
  * [Modifying the Content of a Measurement](#modifying-the-content-of-a-measurement)
    * [Operations](#operations)
  * [Compound Measurement](#compound-measurement)
    * [Indeterminate Scales](#indeterminate-scales)
    * [Predefined Units](#predefined-units)
  * [Creating Your Own Measurement](#creating-your-own-measurement)
    * [Dynamic Measurement](#dynamic-measurement)
  * [Available Units And Documentations](#available-units-and-documentations)
  * [Contribution](#contribution)
<!-- TOC -->

## Installation

### Requirements

This package requires PHP `8.4.1` up.

### Install via Composer

```bash
composer require asika/better-units
```

## Getting Started

### How to Use This Package

This package provides a simple and intuitive way to store and convert measurement values. You can use it to store and
convert various units of measurement, such as time, length, weight, temperature, and more, and pass them between objects
and functions.

In the past, when your objects needed to accept a measurement value, you might have used `int` or `float` to represent
the value. However, this approach has some issues:

- You cannot ensure the unit of the value is correct, which may lead to unit errors.
- You cannot ensure the precision of the value, which may result in precision loss.
- You cannot ensure the range of the value, which may cause overflow issues.
- You cannot ensure the format of the value, which may lead to formatting errors.

For example, the following function calculates the total listening time, but the developer cannot determine the unit of
the `$duration` parameter. It could be seconds, minutes, or even hours, which may lead to calculation errors.

```php
function calcListenTime(int $duration): string {
    return sprintf('%.2f hours', $duration / 3600);
}

// What unit is this? Could be seconds, minutes, or hours
calcListenTime(3600);
```

By using Measurement objects, the function itself does not need to handle unit
details, as the Measurement object will automatically manage unit conversion and precision issues. Developers only need
to pass the measurement object into the function, and the function will automatically convert it to the required
unit and format.

```php
function calcListenTime(Duration $duration): string {
    return $duration->toHours(scale: 4)->format(suffix: ' hours');
}

calcListenTime(4575); // "1.2694 hours"
```

### How to Create Measurement Object

You can create a Measurement as follows. Each Measurement has its own default unit. For example, the default unit of
`Duration` is `seconds`, so when you create a `Duration` object directly, the input value will be stored in `seconds`.

You can immediately convert it to other units, such as `minutes` or `hours`. This package uses
the [brick/math](https://github.com/brick/math) for mathematical operations, so the returned value will be a
`BigDecimal` object.

```php
use Asika\BetterUnits\Duration;

$duration = new Duration(600); // 600 seconds

// Get raw value in seconds
$duration->value; // BigDecimal(600)

// Convert to minutes value
$duration->toMinutes(); // BigDecimal(10)

(string) $duration->toMinutes()->minus(2); // "8"
```

You can also specify the initial unit, as shown in the following examples. The unit can be specified using class
constants or English abbreviations like `minutes` or `min`. (For detailed available units, please refer to the
[documentation](./docs) of each Measurement.)

```php
$duration = new Duration(60, Duration::UNIT_MINUTES); // 10 minutes
$duration = new Duration(60, 'minutes'); // 10 minutes
$duration = new Duration(60, 'min'); // 10 minutes

// Get raw value in minutes
$duration->value; // BigDecimal(60)

// Convert to hour value
$duration->toHours(); // BigDecimal(1)
```

### Rounding Mode [!important]

When the conversion result includes decimals, the default rounding mode is "round down".
Therefore, when converting smaller units to larger units, if the value is insufficient to round up, such as converting
seconds to hours or months, the result may directly be `0`. This is the expected behavior.

You can add the precision parameter `scale: int` to specify the number of decimal places. Additionally, you can use the
`roundingMode: enum` parameter from [brick/math](https://github.com/brick/math) to change the rounding rule.

```php
$duration->toHours(); // BigDecimal(0)
$duration->toHours(scale: 5); // BigDecimal(0.16666)

$duration->toHours(scale: 1, roundingMode: \Brick\Math\RoundingMode::HALF_UP); // BigDecimal(0.17)
```

### Create By Strings

BetterUnits lets you create conversion objects using strings, making unit conversions easier.

When parsing strings, the smallest supported unit is used first, then converted to the default unit. For example, the
smallest unit for `Duration` is `femtoseconds`, and the default unit is `seconds`. So, when you create a `Duration`
object from a string, it is first parsed into `femtoseconds` and then converted to `seconds`.

This ensures the original value, including decimals, is preserved. More details on value conversion and normalization
will follow.

```php
$duration = \Asika\BetterUnits\Duration::parse('10hours 5minutes 30seconds 50ms 100ns 300fs');

$duration->value; // BigDecimal(36330.0500001000003)
```

If you want to parse a string and convert it to another unit, you can add a second `asUnit` parameter.

```php
$duration = Duration::parse(
    '10hours 5minutes 30seconds 50ms 100ns 300fs',
    asUnit: Duration::UNIT_MINUTES
)->value; // BigDecimal(605.500833335000005)

// Peek the current unit of this measurement
echo $duration->unit; // "minutes"
```

Similarly, all functions that involve unit conversion can include the `scale` and `roundingMode` parameters to control
the number of decimal places and rounding rules.

```php
$duration = Duration::parse(
    '10hours 5minutes 30seconds 50ms 100ns 300fs',
    asUnit: Duration::UNIT_MINUTES,
    scale: 3,
    roundingMode: \Brick\Math\RoundingMode::HALF_UP
)->value; // BigDecimal(605.501)
```

The `from()` method is a more general-purpose function. If a string is passed as a parameter, it will parse the string.
If a number is passed, it will directly create a measurement object with the value.

```php
$duration = Duration::from('100seconds');
$duration = Duration::from('3 years 50days 5hours 30minutes', scale: 4, roundingMode: RoundingMode::HALF_UP);
$duration = Duration::from(1200); // 1200 seconds
$duration = Duration::from(500, Duration::UNIT_MINUTES);
```

## Unit Conversion

BetterUnits provides two ways to convert units: one retains the `Measurement` object after conversion, and the other
outputs the value directly.

### Output Values

Use the `to()` or `toXxx()` methods to convert units and output values. All values will be `BigDecimal` objects.

```php
$duration->to(Duration::UNIT_MINUTES); // Use constants
$duration->to('months'); // Months
$duration->to('y'); // Year with shortcut

// Or preset methods
$duration->toMinutes(); // Minutes
$duration->toHours(); // Hours
$duration->toDays(); // Days
```

If you just want to get the current value, you can directly use the `value` property, which will return a `BigDecimal`
object.

```php
$duration->value; // BigDecimal(600)
```

### convertTo() Method

The `convertTo()` method allows you to convert units while maintaining the `Measurement` object with
chaining operations. All modifications to the `Measurement` object are **immutable**, so you must assign the result to a
new variable.

> [!important]
> Additionally, when converting smaller units to larger ones, precision may be lost. Be sure to manually set
> the `scale` and `roundingMode` parameters as needed for the conversion.

```php
$seconds = new Duration(600, 's'); // 600 seconds

// Immutable
$minutes = $seconds->convertTo(Duration::UNIT_MINUTES);

// $seconds still 600 seconds
$seconds->value; // BigDecimal(600)
$minutes->value; // BigDecimal(10)

// Control the precision
$hours = $seconds->convertTo(Duration::UNIT_HOURS, scale: 2, roundingMode: RoundingMode::HALF_UP);

$hours->value; // BigDecimal(0.17)
```

### Precision Control

For safety reasons, BetterUnits uses `RoundingMode::DOWN` from [brick/math](https://github.com/brick/math)
as the default rounding rule during unit conversions, discarding all decimal places. This means that even `59` seconds
will be converted to `0` minutes.

> [!important]
> If you use the `convertTo()` method, all discarded digits during the conversion process will be removed and cannot be
> restored,
> resulting in precision loss. The following example demonstrates this situation:

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES) // 0 minutes
    ->convertTo(Duration::UNIT_SECONDS); // 0 seconds

$duration->value; // BigDecimal(0) - All precision lost
```

During the conversion process, if indefinite-length decimals are allowed, unexpected minor precision
loss may occur, and engineers or users may not notice it at all.

Therefore, this package
requires developers to consciously specify precision and rounding rules to ensure that any precision loss during the
conversion process is anticipated and controlled.

If you want to specify precision and rounding rules, you can add the `scale` and `roundingMode` parameters during
conversion to manually control the range of precision loss.

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, scale: 8) // 0.98333333 minutes
    ->convertTo(Duration::UNIT_SECONDS, scale: 8); // 58.9999998 seconds

// Back to seconds
$duration->value; // BigDecimal(58.9999998)
```

The following example shows how to use high precision when converting to larger units and apply half-up rounding when
converting to smaller units. This ensures that the original value in seconds can be accurately restored.

```php
new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, 8, RoundingMode::HALF_UP) // 0.98333333 minutes
    ->convertTo(Duration::UNIT_SECONDS, 0, RoundingMode::HALF_UP);

// BigDecimal(59)
```

## Units

BetterUnits can represent units using constants or English unit strings. For example, with `Duration`, the supported
units include:

- `Duration::UNIT_FEMTOSECONDS` (fs, femtoseconds, femtosecond)
- `Duration::UNIT_PICOSECONDS` (ps, picoseconds, picosecond)
- `Duration::UNIT_NANOSECONDS` (ns, nanoseconds, nanosecond)
- `Duration::UNIT_MICROSECONDS` (μs, us, microseconds, microsecond)
- `Duration::UNIT_MILLISECONDS` (ms, milliseconds, millisecond)
- `Duration::UNIT_SECONDS` (s, sec, seconds, second)
- `Duration::UNIT_MINUTES` (min, m, minutes, minute)
- `Duration::UNIT_HOURS` (h, hour, hours)
- `Duration::UNIT_DAYS` (d, day, days)
- `Duration::UNIT_WEEKS` (w, week, weeks)
- `Duration::UNIT_MONTHS` (mo, month, months)
- `Duration::UNIT_YEARS` (y, year, years)

Any part that allows inputting units for conversion or parsing strings can use these constants or strings to represent
units.

It does not matter if there is a space between the unit and the value. For example, `2hours`, `2 hours`, `2hr`,
and `2 hr` are all acceptable formats.

Depending on the Measurement, singular and plural forms like `year` and `years`
are usually interchangeable (except for some units where singular and plural forms have specific differences, as defined
by the Measurement).

Below are examples of input for parsing:

```php
\Asika\BetterUnits\Duration::parse('10 hours 5 minutes 30 seconds 50ms 100ns 300fs');
\Asika\BetterUnits\Duration::parse('3y 2mo 1w 2d 3h 4min 5s 6ms 7μs 8ns 9fs');
```

## Formatting

Measurement provides several functions to display formatted strings. These functions are generally available for all
Measurement types. For now, we will use `Duration` as an example.

### `format()`

The `format()` method formats the current value based on its unit. By default, it appends the unit's original string as
a suffix directly to the value.

```php
$duration = new Duration(59, 's');

$duration->value; // BigDecimal(59)

$duration->format(); // "59seconds"
```

The first parameter, `suffix`, specifies the suffix format for the output. This parameter can be:

- A plain string, used as the suffix.
- A string containing `%s`, which will be used as a `sprintf` template.
- A `Closure` that receives the value and object during execution and returns a string. This is useful for integrating
  with frameworks like i18n.
    - Format: `Closure(BigDecimal $value, string $unit, AbstractMeasurement $measurement): string`

```php
$duration->format(); // "59seconds"

$duration->format(suffix: ' SEC'); // "59 SEC"

// Use for localization, this is "seconds" in Chinese. 
$duration->format(suffix: '秒'); // "59秒"

// Use template string
$duration->format(suffix: 'The Timeout is: %s'); // "The Timeout is: 59"

// Closure
$duration->format(
    function (BigDecimal $value, string $unit, AbstractMeasurement $measurement): string {
        // Integrate with i18n or other frameworks
        return Translator::trans('app.timeout.unit.seconds', value: $value->toScale(2), unit: $unit);
    }
); // "Timeout: 59.00 seconds"
```

`format()` can specify the output unit and will immediately convert and display the value in that unit. Since it formats
into a single unit, precision and rounding rules must also be considered.

```php
$duration = new Duration(59, 's');

$duration->format(unit: Duration::UNIT_MINUTES); // 0minutes

$duration->format(unit: Duration::UNIT_MINUTES, scale: 8); // "0.98333333minutes"
```

If you have already set the precision and rounding rules when parsing values or using the `convertTo()` method, you
don't need to specify the `scale` when calling `format()`. It will use the current precision settings to display the
value.

```php
new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, scale: 8) // The scale will save into the measurement
    ->format(); // "0.98333333minutes"
```

### `humanize()`

`humanize()` is used to convert the current unit into a more readable format. It automatically breaks down the value
into units displayed from largest to smallest.

```php
$duration = Duration::parse('162231024996102500ns');

echo $duration->humanize(); 
// 5years 1month 3weeks 1day 5hours 46minutes 24seconds 996milliseconds 102microseconds 500nanoseconds
```

This is ideal for presenting final statistics to end-users. Below is an example where we show the total music playback
time for this month to the end-user:

```php
$seconds = 465718;
$totalPlaySeconds = Duration::from($seconds, 's');
echo $totalPlaySeconds->humanize(); // 5days 9hours 21minutes 58seconds
```

The first parameter, `formats`, can accept a `Closure` to control the formatting logic for all units. This is
particularly useful for integrating with frameworks like i18n.  
The second parameter, `divider`, allows you to specify the separator between units, with the default being a space.

```php
$totalPlaySeconds->humanize(
    formats: fn(BigDecimal $value, string $unit) => $value . ' ' . strtoupper($unit),
    divider: ' / '
);
// 5 DAYS / 9 HOURS / 21 MINUTES / 58 SECONDS
```

But we usually only need to display up to hours and do not need to convert hours into days. We can provide a unit array
to the first parameter `formats` to control the units we want to display.

```php
echo $totalPlaySeconds->humanize(
    formats: [
        Duration::UNIT_HOURS,
        Duration::UNIT_MINUTES,
        Duration::UNIT_SECONDS,
    ],
    divider: ', '
);
// 129hours, 21minutes, 58seconds
```

If `formats` is an array, you can also provide a Closure for formatting. Below is an example of using a simplified time
expression, which is suitable for displaying durations in media players:

```php
$format = fn(\Brick\Math\BigDecimal $value) => str_pad((string) $value, 2, '0', STR_PAD_LEFT);
echo $totalPlaySeconds->humanize(
    formats: [
        Duration::UNIT_HOURS => $format,
        Duration::UNIT_MINUTES => $format,
        Duration::UNIT_SECONDS => $format,
    ],
    divider: ':'
);
// 129:21:58
```

The `humanize()` method has an `options` parameter that supports two directives:

`OPTION_NO_FALLBACK`: Controls whether to display units with a value of 0 when the total value is 0.

```php
$duration = new Duration(0, 's');

echo $duration->humanize(); // "0seconds"
echo $duration->humanize(options: Duration::OPTION_NO_FALLBACK); // ""
```

`OPTION_KEEP_ZERO` controls whether units with a value of 0 should be displayed.

```php
$duration = new Duration(1000500, 's');
echo $duration->humanize(); // "1week 4days 13hours 55minutes"

echo $duration->humanize(options: Duration::OPTION_KEEP_ZERO);
// 0years 0months 1week 4days 13hours 55minutes 0seconds 0milliseconds
// 0microseconds 0nanoseconds 0picoseconds 0femtoseconds
```

### Default Formatting Handler

A `Measurement` can register a default formatting handler. When `format()` or `humanize()` is called without specifying
formatting parameters, this handler will be used.
The example below demonstrates a handler that changes the suffix based on whether the value is singular or plural. Note
that the `suffixFormatter` and the `format()` handler parameters are different.
The first parameter is the default suffix, and the second parameter is the unit value, which can be used to make
necessary adjustments.

```php
$measurement = $measurement->withSuffixFormatter(
    function (string $suffix, BigDecimal $value, string $unit, Duration $measurement): string {
        if ($value->isEqualTo(1)) {
            $suffix = StrNormalizer::singularize($suffix);
        } else {
            $suffix = StrNormalizer::pluralize($suffix);
        }

        return $value . ' ' . $suffix;
    }
);
```

### `serialize()`

`serialize()` is similar to `humanize()`, but it does not allow customizing the format string. It converts a
`Measurement` object into a serializable string, making it convenient for storing in a database or cache. You can use
the `parse()` method to convert the string back into a `Measurement` object.

```php
$duration = new Duration(1000500, 's');
$serialized = echo $duration->serialize(); // 1week 4days 13hours 55minutes

$newDuration = Duration::parse($serialized);

$duration->value->equals($newDuration->value); // TRUE
```

serialize() can also specify the output unit, allowing the value to be serialized directly into a specific unit.

```php
$duration = new Duration(1000500, 's');
echo $duration->serialize(
    [
        Duration::UNIT_HOURS,
        Duration::UNIT_MINUTES,
    ]
); // 277hours 55minutes
```

Note that `serialize()` does not support decimals. It is recommended to serialize using the smallest representable unit
to avoid precision loss.

```php
$duration = new Duration(1000500, 's');
echo $duration->serialize(
    [
        Duration::UNIT_FEMTOSECONDS,
    ]
);
// 1000500000000000000000femtoseconds
```

### `serializeCallback()`

`serializeCallback()` is a powerful tool that allows you to customize the serialized string format, integrate with
frameworks for translation, or display data in a user-friendly way.

This function accepts a `Closure` with two parameters:
`Closure(AbstractMeasurement $remainder, array<string, BigDecimal> $sortedUnits): string`. The first parameter is the
`Measurement` object converted to the atomUnit, and the second parameter is an array of units and values sorted by their
conversion rates.

Below is an example using `Duration`:

```php
$duration = new Duration(1000500, 's');
echo $duration = $duration->serializeCallback(
    function (Duration $remainder, array $sortedUnits) {
        $text = [];

        foreach ($sortedUnits as $unit => $ratio) {
            [$extracted, $remainder] = $remainder->withExtract($unit);

            if ($extracted->isZero()) {
                continue;
            }

            // You don't need to set $scale parameter here, all extracted values are integer.
            $text[] = $extracted->format();
            
            if ($remainder->isZero()) {
                break; // [Optional] No more remainder, stop here
            }
        }

        return implode(' ', $text);
    }
); // 1week 4days 13hours 55minutes
```

`$sortedUnits` is a sorted array, arranged from the largest unit to the smallest based on their conversion rates. This
allows extracting values starting from the largest unit. If the remaining value is not enough to extract a full unit, it
will be passed to the next smaller unit for extraction, continuing until the smallest atomic unit is reached. All
extracted values will be integers, as any remainder will be carried over to the next unit, so you don't need to worry
about precision issues during formatting.

The `withExtract()` method extracts the value of a specified unit from a `Measurement` object and returns a tuple
`[extracted, remainder]`. For example, if the largest unit is `year`, it will try to extract the integer value of `year`
into a separate `Measurement` object called `extracted`, while the remaining fractional value will be stored in
`remainder`. The `remainder` is then passed to the next iteration for `months` extraction, and this process continues
until the `remainder` becomes 0 or all units have been processed.

Thanks to the powerful extraction capability of `withExtract()`, you can fully customize the list of units to serialize.
The units don't need to be consecutive (but you must ensure the units are ordered correctly by size).

```php
$duration = new Duration(6000500, 's');
echo $duration = $duration->serializeCallback(
    function (Duration $remainder) {
        $text = [];

        $units = [
            Duration::UNIT_MONTHS,
            // We ignore weeks and days
            Duration::UNIT_HOURS,
            Duration::UNIT_MINUTES,
            Duration::UNIT_SECONDS,
        ];

        foreach ($units as $unit) {
            [$extracted, $remainder] = $remainder->withExtract($unit);

            if ($extracted->isZero()) {
                continue;
            }

            $text[] = $extracted->format();
        }

        return implode(' ', $text);
    }
); // 2months 206hours 20seconds
```

Note: If the current unit of your `Measurement` is smaller than the smallest unit you are serializing, precision loss
may occur. This is because `withExtract()` only extracts the integer part, and the remaining decimal part will be
discarded. Alternatively, you can manually output the final `remainder` as a decimal string.

## Unit Management

Each `Measurement` has several unit-related settings. Here's a brief introduction:

- `$measurement->atomUnit`: The smallest indivisible unit of the `Measurement`, such as `femtoseconds` for `Duration`.
- `$measurement->baseUnit`: The base unit for exchange rates, where the ratio is `1`. For example, `seconds` for
  `Duration`.
- `$measurement->defaultUnit`: The default unit used when creating a `Measurement` if no unit is specified. This is
  usually the same as `baseUnit` but not always. For example, `seconds` for `Duration`.
- `$measurement->unit`: The current unit of the `Measurement`, which can be manually specified during creation or
  changed using the `convertTo()` method.

When using the `parse()` method to parse a string, all `Measurement` values are automatically converted to the
`atomUnit` first, then to the `defaultUnit` or the specified unit.

### Restrict Available Units

Sometimes, you may want to limit the units a `Measurement` can handle. For example, you might want `Duration` to ignore
the `weeks` unit or restrict `FileSize` to only use byte-based units.

You can use the `withAvailableUnits()` method to restrict the available units. This ensures that only the specified
units can be used for conversions and outputs.

```php
$duration = $duration->withAvailableUnits(
    [
        Duration::UNIT_SECONDS,
        Duration::UNIT_MINUTES,
        Duration::UNIT_HOURS,
        Duration::UNIT_DAYS,
    ]
);
$duration = $duration->withParse('3 days 5 hours 30 minutes');

$duration = $duration->withParse('2 years 3 days'); // Exception: Unknown unit "years"
```

Each Measurement has commonly used units, which can be found in its documentation or by checking the constants defined
in the Measurement class.

### Customizing or Adding Units

Measurement supports customizing or adding new units. You can use the `withAddedUnitExchangeRate()` method to add a new
unit, which will be included in the list of available units for the Measurement.
The rate of the unit is based on the unit defined as `1` in the Measurement. For example, the base unit for `Duration`
is `seconds`, with a rate of `1`.
We can try adding a `centuries` unit and set its rate to `3153600000` seconds (the number of seconds in 100 years).

```php
$duration = new Duration()->withAddedUnitExchangeRate('centuries', 3_153_600_000);

$duration->withParse('350years')
    ->format(unit: 'centuries', scale: 1); // "3.5centuries"
```

To make the `centuries` unit recognize various abbreviations, we can use the `withUnitNormalizer()` method to set a unit
normalizer. This allows `centuries` to support abbreviations like `century`, `c`, etc. This normalizer is an additional
feature and will not override the behavior of built-in units.

```php
$duration = $duration->withUnitNormalizer(
    function (string $unit): string {
        return match ($unit) {
            'centuries', 'century', 'cent', 'cents', 'c' => 'centuries',
            default => $unit,
        };
    }
);
```

If you want the `Measurement` to be serializable, you can use a callable pointing to a static function as the
normalizer. This avoids issues where closures cannot be serialized. (Alternatively, you can consider
using [laravel/serializable-closure](https://github.com/laravel/serializable-closure).)

```php
$duration = $duration->withUnitNormalizer(
    [MyCenturiesHelper::class, 'normalizeUnit'] // 靜態函式 normalizeUnit
);
```

If you want to dynamically set the number of seconds in `centuries`, you can use any unit for conversion. For example,
we can calculate it based on the rate of years.

```php
$duration = new Duration();
$yearRate = $duration->getUnitExchangeRate(Duration::UNIT_YEARS);
$duration = $duration->withAddedUnitExchangeRate(
    'centuries',
     $yearRate->multipliedBy(100)
);
```

> [!note]
> Note this value is an approximation. The actual number of seconds in a year may vary depending on the calendar system.
> For more details, please refer to the documentation of `Duration`.

### Changing Conversion Rates

Each `Measurement` has a different base unit that represents `1`. For example, the base unit for `Duration` is
`seconds`, while for `FileSize`, it is `bytes`.

The `unitExchanges` for `Duration` look like this:

```php
    protected array $unitExchanges = [
        self::UNIT_FEMTOSECONDS => 1e-15,
        self::UNIT_PICOSECONDS => 1e-12,
        self::UNIT_NANOSECONDS => 1e-9,
        self::UNIT_MICROSECONDS => 1e-6,
        self::UNIT_MILLISECONDS => 1e-3,
        self::UNIT_SECONDS => 1.0,
        self::UNIT_MINUTES => 60.0,
        self::UNIT_HOURS => 3600.0,
        self::UNIT_DAYS => 86400.0,
        self::UNIT_WEEKS => 604800.0,
        self::UNIT_MONTHS => self::MONTH_SECONDS_COMMON,
        self::UNIT_YEARS => self::YEAR_SECONDS_COMMON,
    ]
```

For certain reasons, if you need to change the conversion rate of the base unit, you can use the
`withUnitExchangeRate()` method to set a new base unit rate. Below is an example where the rate of femtoseconds is set
to `1`, making it the new base unit. This function will reset all available units, allowing you to add or remove units
as needed.

```php
$d->withUnitExchanges(
    [
        Duration::UNIT_FEMTOSECONDS => 1.0,
        Duration::UNIT_PICOSECONDS => 1000.0,
        Duration::UNIT_NANOSECONDS => 1_000_000.0,
        Duration::UNIT_MICROSECONDS => 1_000_000_000.0,
        Duration::UNIT_MILLISECONDS => 1_000_000_000_000.0,
        Duration::UNIT_SECONDS => 1_000_000_000_000_000.0,
        Duration::UNIT_MINUTES => 60_000_000_000_000_000.0,
        Duration::UNIT_HOURS => 3_600_000_000_000_000_000.0,
        Duration::UNIT_DAYS => 86_400_000_000_000_000_000.0,
        Duration::UNIT_WEEKS => 604_800_000_000_000_000_000.0,
        Duration::UNIT_MONTHS => 2_592_000_000_000_000_000_000.0, // 30 days
        Duration::UNIT_YEARS => 31_536_000_000_000_000_000_000.0, // 365 days
    ],
    atomUnit: Duration::UNIT_FEMTOSECONDS,
    defaultUnit: Duration::UNIT_SECONDS
);
```

Since the subsequent exchange rates might exceed the integer limit, it is recommended to represent them as strings or
floating-point numbers. The `unitExchanges` property can accept formats such as `int`, `float`, `string`, or
`BigDecimal`. These values will later be unified into `BigDecimal` for consistent calculations.

This function requires you to explicitly redefine both the `atomUnit` and `defaultUnit` because the units and exchange
rates in a `Measurement` are closely related.

The `defaultUnit` does not necessarily have to be the same as the `baseUnit`. It is used as the default unit when
creating a `Measurement` without specifying a unit.

You can also use the `withAddedUnitExchangeRate()` method to add new units or the `withoutUnitExchangeRate()` method to
remove units without affecting the existing ones.

### Other Unit Adjustments

Each type of measurement unit has its own settings, which can be used to adjust the behavior or calculation logic of the
units.  
For example, `Duration` can configure calendar rules to calculate the number of seconds in a year or a month.

```php
$duration = new \Asika\BetterUnits\Duration();
$duration = $duration->withAnomalisticCalendar(); // Use Anomalistic Calendar for year/month calculations

// you must parse values after setting calendar
$duration->withParse('1 year')->toSeconds(); // 31556952 seconds (Anomalistic year)
```

Alternatively, `FileSize` supports both IS and IEC unit standards, allowing you to configure which standard to use for
unit calculations.

```php
$fs = new \Asika\BetterUnits\FileSize();
$fs = $fs->withOnlyBytesBinary(); // Use only binary bytes (IEC) for calculations (KiB, MiB, GiB, etc.)

$fs->withParse('100KiB'); // OK
$fs->withParse('100KB'); // ERROR: Unknown base unit: KB
```

For more detailed configuration methods, please refer to the documentation for each measurement unit.

## Get the Unit Closest to 1

The `nearest()` method in Measurement allows you to find the unit closest to 1. This method calculates the most
human-readable unit based on the current value and unit ratio.

```php
$fs = \Asika\BetterUnits\FileSize::from('8500KiB');
$nearest = $fs->nearest(scale: 2, RoundingMode::HALF_UP)->format(); // 8.31MiB
```

## Modifying the Content of a Measurement

Measurement objects are immutable, meaning that any operation on a Measurement will return a new Measurement object
without modifying the original one.

We provide a series of methods to modify the content of a Measurement. If you want to change the value and unit of a
Measurement, you can use the `with()` method. This method changes the value and unit without performing any conversion.

```php
$measurement = $measurement->with(100, 'seconds'); // Returns a new Measurement with 100 seconds
```

If you provide a `BigDecimal` with a specific scale, the `Measurement` object will retain this scale. This ensures that
precision is preserved during subsequent conversions and formatting.

```php
$measurement = $measurement->with(BigDecimal::of(100.25), 'hours');

$measurement->format(); // "100.25hours"
```

If you only want to change the value while keeping the unit, or change the unit while keeping the value, you can use the
`withValue()` or `withUnit()` methods.

```php
$measurement = \Asika\BetterUnits\Duration::from(100, 'seconds');

$measurement->withValue(300); // Returns a new Duration with 300 seconds, keep unit as seconds
$measurement->withUnit(Duration::UNIT_HOURS); // Returns a new Duration with unit hours, keep value as 300
```

### Operations

Measurement objects support basic arithmetic operations such as addition, subtraction, multiplication, and division. The
values used in these operations can be `BigNumber`, numbers, or strings.

```php
$new = $measurement->plus(100); // Returns a new Measurement with value + 100
$new = $measurement->minus(50.0); // Returns a new Measurement with value - 50
$new = $measurement->multipliedBy('2'); // Returns a new Measurement with value * 2
$new = $measurement->dividedBy(BigNumber::of(2)); // Returns a new Measurement with value / 2
```

The `plus()` and `minus()` methods can accept another Measurement object for calculations. They will automatically
convert the unit to match the original Measurement. However, you must manually specify the precision to avoid precision
loss after conversion. Additionally, the default `roundingMode` for addition and subtraction is `UNNECESSARY`, so it is
highly recommended to explicitly specify the `RoundingMode` to prevent errors.

```php
$measurement = new Duration(120, 'seconds'); // 120 seconds
$new = $measurement->plus(new Duration(2, 'minutes'), scale: 2, RoundingMode::HALF_UP); // Returns a new Duration with 240 seconds
$new = $measurement->minus(new Duration(2500, 'ms'), scale: 2, RoundingMode::HALF_UP); // Returns a new Duration with 117.5 seconds
```

If you need to perform more complex calculations, you can directly access the `value` property, which is a `BigDecimal`
object. You can use `BigDecimal` methods for calculations and then create a new Measurement object using the `with()` or
`withValue()` methods. These methods also accept a `Closure` as a parameter, allowing for more flexible calculations.

```php
// Returns a new Measurement with value / 2
$measurement = $measurement->with(
    $measurement->value->dividedBy(2, scale: 2, RoundingMode::UP)
);

// Calculate by a Closure
$measurement = $measurement->with(
    fn (BigDecimal $value, string $unit, $measurementObject) => $measurement->value->power()
);
```

## Compound Measurement

Some Measurements require combining multiple units, referred to as `num` (numerator) and `deno` (denominator),
representing the units in the numerator and denominator.

For example, Speed requires both distance and time, making it a Compound Measurement composed of `Length` (numerator)
and `Duration` (denominator). When expressing the unit of Speed, it will be the unit of `Length` divided by the unit of
`Duration`, such as `m/s` or `km/h`.

```php
$speed = Speed::from('100 km/h'); // 100 kilometers per hour
$speed->convertTo('m/s', scale: 4); // 27.7777m/s
```

### Indeterminate Scales

Since this library always converts to the smallest atom unit first before converting to the target unit, and the
Compound Measurement object may perform multiple conversions in 2-3 steps internally, there might be cases where the
number of decimal places cannot be determined, leading to unexpected precision loss.

To prevent such issues, Compound Measurement uses a default scale of `99` decimal places for internal conversions. If
your unit conversion process exceeds this decimal scale, you can try increasing the `intermediateScale` to ensure
accurate calculations, or decreasing the `intermediateScale` value to improve calculation speed.

```php
// Set higher
$compoundMeasurement = $compoundMeasurement->withIntermediateScale(299);

// Set lower
$compoundMeasurement = $compoundMeasurement->withIntermediateScale(20);
```

Here we demonstrate a case with `Speed` object, where an insufficient `intermediateScale` causes unexpected errors:

```php
$mps = new Speed()
    ->withIntermediateScale(20)
    ->withParse('1kph')
    ->toMps(scale: 10);

echo $mps;
// Expect: 0.2777777777
// Actual: 0.27777
```

Now we set intermediateScale to a higher value to ensure precision

```php
$mps = new Speed()
    ->withIntermediateScale(99)
    ->withParse('1kph')
    ->toMps(scale: 10);

show((string) $mps);
// Good: 0.2777777777
```

### Predefined Units

Each Compound Measurement has some predefined units, which are commonly used international standard unit names, such as:

- `kph` (km/h, kilometers per hour)
- `mph` (miles per hour)
- `mps` (m/s, meters per second)
- `knots` (knots, nautical miles per hour)

These units can be directly used in the `from()` or `convertTo()` methods, making it convenient to create or convert
Compound Measurements.

```php
$speed = Speed::from('100 kph'); // 100 kilometers per hour
$speed->convertTo('mps', scale: 4); // 27.7777m/s
```

## Creating Your Own Measurement

Here is a simple example. To create a custom Measurement, you need to extend the `AbstractBasicMeasurement` class.  
There are three required properties to implement:  
`$atomUnit` represents the smallest indivisible unit,  
`$defaultUnit` is the default unit,  
and `$unitExchanges` defines the conversion rates between units.

Make sure to include at least one base unit with a rate of `1`, as some calculations may fail without it.

The `normalizeUnit()` method is optional. It is used to convert input unit strings into supported units and is called
during string parsing or unit conversion.

```php
class ScreenMeasurement extends AbstractBasicMeasurement
{
    public const string UNIT_PX = 'px';
    public const string UNIT_PT = 'pt';
    public const string UNIT_EM = 'em';
    public const string UNIT_REM = 'rem';

    public string $atomUnit = self::UNIT_PX;

    public string $defaultUnit = self::UNIT_PX;

    protected array $unitExchanges = [
        self::UNIT_PX => 1.0,
        self::UNIT_PT => 1.3333333333, // 1pt = 1/72 inch, 1px = 96/72 inch
        self::UNIT_EM => 16.0, // Assuming 1em = 16px
        self::UNIT_REM => 16.0, // Assuming 1rem = 16px
    ];

    protected function normalizeUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'px', 'pixel', 'pixels' => self::UNIT_PX,
            'pt', 'point' => self::UNIT_PT,
            'em', 'em quad' => self::UNIT_EM,
            'rem', 'root em' => self::UNIT_REM,
            default => $unit,
        };
    }
}
```

### Dynamic Measurement

You can use `DynamicMeasurement` to create a dynamic Measurement that allows you to set units and exchange rates at
runtime.

Below is an example of a dynamic currency conversion Measurement. This is useful for e-commerce systems where exchange
rates and currencies can be configured dynamically.

```php
use Asika\BetterUnits\DynamicMeasurement;

$currency = new DynamicMeasurement(
    atomUnit: 'USD',
    defaultUnit: 'USD',
    // Example exchange rate
    unitExchanges: [
        'TWD' => $dailyExchangeRate->getRate('TWD'), // 0.33
        'CNY' => $dailyExchangeRate->getRate('CNY'), // 0.15
        'JPY' => $dailyExchangeRate->getRate('JPY'), // 0.007
        'USD' => 1.0,
        'EUR' => $dailyExchangeRate->getRate('EUR'), // 1.1
        'GBP' => $dailyExchangeRate->getRate('GBP'), // 1.3
    ]
);
$currency = $currency->withUnitNormalizer(
    fn(string $unit): string => match (strtolower($unit)) {
        'usd' => 'USD',
        'eur' => 'EUR',
        'gbp' => 'GBP',
        'cny' => 'CNY',
        'twd' => 'TWD',
        'jpy' => 'JPY',
        default => $unit,
    }
);

$currency = $currency->withParse('100USD')
    ->convertTo('EUR', scale: 2);

echo $currency->format(); // 90.9EUR
```

## Available Units And Documentations

- [Measurements List](./docs)
    - Basic Measurement
        - [Area](./docs/area.md)
        - [Duration](./docs/duration.md)
        - [Energy](./docs/energy.md)
        - [FileSize](./docs/filesize.md)
        - [Length](./docs/length.md)
        - [Volume](./docs/volume.md)
        - [Weight](./docs/weight.md)
    - Compound Measurement
        - [Speed](./docs/speed.md)
        - [Bitrate](./docs/bitrate.md)

## Contribution

If you find any errors and know how to fix them, feel free to open a Pull Request. This will help us improve the fixing
faster.

Since I cannot precisely verify all unit conversion rates, if you find any incorrect conversion rates in code or 
documentation, please make sure to include reference sources in the Issue or Pull Request.
