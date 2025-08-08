# Duration Measurement

Duration is an object for calculating time length units, usually used to measure the duration of events or operations.

<!-- TOC -->
* [Duration Measurement](#duration-measurement)
  * [Create](#create)
  * [Available Units](#available-units)
  * [Conversion](#conversion)
  * [Formatting](#formatting)
  * [Calendar](#calendar)
    * [Available Calendar Constants](#available-calendar-constants)
    * [Quick Setting](#quick-setting)
  * [PHP DateTime Conversion](#php-datetime-conversion)
    * [DateInterval](#dateinterval)
    * [Convert to DateTime](#convert-to-datetime)
<!-- TOC -->

## Create

To create a Duration instance, you can use the following methods:

```php
use Asika\BetterUnits\Duration;

$duration = new Duration(3600); // 3600 seconds
$duration = new Duration(100, Duration::UNIT_MINUTES); // 100 minutes

$duration = Duration::parse('2 hours 30 minutes'); // 2 hours and 30 minutes

$duration = Duration::from(300, 'days');
$duration = Duration::from('300 days');
```

## Available Units

- Atom Unit: `femtoseconds`
- Default Unit: `seconds`
- Base Unit: `seconds`

| Unit           | Constant            | Aliases                                   | Ratio (to seconds)  | Description      |
|----------------|---------------------|-------------------------------------------|---------------------|------------------|
| `femtoseconds` | `UNIT_FEMTOSECONDS` | `fs`, `femtosecond`, `femtoseconds`       | `0.000000000000001` | Femtosecond      |
| `picoseconds`  | `UNIT_PICOSECONDS`  | `ps`, `picosecond`, `picoseconds`         | `0.000000000001`    | Picosecond       |
| `nanoseconds`  | `UNIT_NANOSECONDS`  | `ns`, `nanosecond`, `nanoseconds`         | `0.000000001`       | Nanosecond       |
| `microseconds` | `UNIT_MICROSECONDS` | `μs`, `us`, `microsecond`, `microseconds` | `0.000001`          | Microsecond      |
| `milliseconds` | `UNIT_MILLISECONDS` | `ms`, `millisecond`, `milliseconds`       | `0.001`             | Millisecond      |
| `seconds`      | `UNIT_SECONDS`      | `s`, `sec`, `second`, `seconds`           | `1`                 | Second           |
| `minutes`      | `UNIT_MINUTES`      | `m`, `min`, `minute`, `minutes`           | `60`                | Minute           |
| `hours`        | `UNIT_HOURS`        | `h`, `hr`, `hour`, `hours`                | `3600`              | Hour             |
| `days`         | `UNIT_DAYS`         | `d`, `day`, `days`                        | `86400`             | Day              |
| `weeks`        | `UNIT_WEEKS`        | `w`, `week`, `weeks`                      | `604800`            | Week             |
| `months`       | `UNIT_MONTHS`       | `mo`, `month`, `months`                   | `2629440`           | Month (variable) |
| `years`        | `UNIT_YEARS`        | `y`, `year`, `years`                      | `31536000`          | Year (variable)  |

## Conversion

You can use the `to()` or `toXxx()` methods to convert Duration to other unit values:

```php
$duration->toSeconds();
$duration->toMinutes(scale: 2, RoundingMode::HALF_UP);
```

Supported functions

- `toFemtoseconds()`
- `toPicoseconds()`
- `toNanoseconds()`
- `toMicroseconds()`
- `toMilliseconds()`
- `toSeconds()`
- `toMinutes()`
- `toHours()`
- `toDays()`
- `toWeeks()`
- `toMonths()`
- `toYears()`

## Formatting

Duration is suitable for displaying elapsed time, listening time, etc. If you store the user's listening time as
seconds, the following demonstrates how to convert it to a human-readable display:

```php
$seconds = 465718;
$totalPlaySeconds = Duration::from($seconds, 's');
echo $totalPlaySeconds->humanize(); // 5days 9hours 21minutes 58seconds
```

Since we expect the maximum unit for listening time to be hours, we can specify the output units in the `humanize()`
method:

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

To display in a player format like `24:59:33`, you can set it up as follows:

```php
$format = fn(BigDecimal $value) => sprintf('%02d', (string) $value);

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

For custom display methods, see [serializeCallback()](../README.md#serializecallback)

## Calendar

Due to the fact that the calculation of years and months does not have a standard length, but changes
according to the calendar used, to accurately calculate the duration in years and months, it is necessary to specify the
calendar you want to use.

`Duration` uses the common calendar by default, with a year calculated as 365 days (`31536000` seconds) and a month as
30 days (`2629440` seconds).
This is suitable for software that is not sensitive to long periods, such as systems that calculate monthly usage.  
However, for systems that require cross-month and cross-year duration statistics, it is not accurate enough.

`Duration` provides a series of calendar units and their corresponding seconds. You can set them using the
`withYearSeconds()` and `withMonthSeconds()` methods.  
The following demonstrates how to set the [Anomalistic](https://en.wikipedia.org/wiki/Anomalistic_year) calendar:

```php
$duration = new Duration()
    ->withYearSeconds(Duration::MONTH_SECONDS_ANOMALISTIC)
    ->withMonthSeconds(Duration::MONTH_SECONDS_ANOMALISTIC);

$duration->withParse('1year 2months')
    ->toSeconds(); // BigDecimal(7142139.36)
```

### Available Calendar Constants

Note that except for the common calendar, the number of seconds is an average value, not fixed. It changes as the Earth
moves, and the value may vary each year. This package can only calculate using average seconds. If you need to calculate
durations based on specific years, please use a suitable calendar library.

| Constant                    | Calendar                                                        | Seconds       | Notes                               |
|-----------------------------|-----------------------------------------------------------------|---------------|-------------------------------------|
| `YEAR_SECONDS_COMMON`       | [Common](https://en.wikipedia.org/wiki/Common_year)             | `31536000`    | 365 days                            |
| `YEAR_SECONDS_LEAP`         | [Leap](https://en.wikipedia.org/wiki/Leap_year)                 | `31622400`    | 366 days                            |
| `YEAR_SECONDS_GREGORIAN`    | [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar)   | `31556952`    | Average year length, mixed calendar |
| `YEAR_SECONDS_JULIAN`       | [Julian](https://en.wikipedia.org/wiki/Julian_year_(astronomy)) | `31557600`    | 365.25 days                         |
| `YEAR_SECONDS_TROPICAL`     | [Tropical](https://en.wikipedia.org/wiki/Tropical_year)         | `31556925.97` | Sidereal year                       |
| `YEAR_SECONDS_SIDEREAL`     | [Sidereal](https://en.wikipedia.org/wiki/Sidereal_year)         | `31558149.76` | Sidereal year                       |
| `YEAR_SECONDS_ANOMALISTIC`  | [Anomalistic](https://en.wikipedia.org/wiki/Anomalistic_year)   | `31558432.55` | Perihelion year                     |
| `YEAR_SECONDS_DRACONIC`     | [Draconic](https://en.wikipedia.org/wiki/Draconic_year)         | `29947971`    | Nodal year                          |
| `YEAR_SECONDS_GAUSSIAN`     | [Gaussian](https://en.wikipedia.org/wiki/Gaussian_year)         | `31558196.01` | Gaussian year                       |
| `MONTH_SECONDS_COMMON`      | [Common](https://en.wikipedia.org/wiki/Common_year)             | `2629440`     | 30.44 days                          |
| `MONTH_SECONDS_GREGORIAN`   | [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar)   | `2629746`     | 30.436875 days                      |
| `MONTH_SECONDS_JULIAN`      | [Julian](https://en.wikipedia.org/wiki/Julian_year_(astronomy)) | `2629800`     | 30.4375 days                        |
| `MONTH_SECONDS_TROPICAL`    | [Tropical](https://en.wikipedia.org/wiki/Tropical_month)        | `2360584.51`  | 27.32158 days                       |
| `MONTH_SECONDS_SIDEREAL`    | [Sidereal](https://en.wikipedia.org/wiki/Sidereal_month)        | `2360591.5`   | 27.321661 days                      |
| `MONTH_SECONDS_ANOMALISTIC` | [Anomalistic](https://en.wikipedia.org/wiki/Anomalistic_month)  | `2380713.12`  | 27.55455 days                       |
| `MONTH_SECONDS_DRACONIC`    | [Draconic](https://en.wikipedia.org/wiki/Draconic_month)        | `2351135.81`  | 27.21222 days                       |
| `MONTH_SECONDS_28DAYS`      | Fixed 28 days                                                   | `2419200`     | Fixed 28 days                       |
| `MONTH_SECONDS_29DAYS`      | Fixed 29 days                                                   | `2505600`     | Fixed 29 days                       |
| `MONTH_SECONDS_30DAYS`      | Fixed 30 days                                                   | `2592000`     | Fixed 30 days                       |
| `MONTH_SECONDS_31DAYS`      | Fixed 31 days                                                   | `2678400`     | Fixed 31 days                       |

### Quick Setting

Duration provides quick setting methods for calendars, you can directly use the following methods to set, without
separately setting years and months.

```php
$duration = $duration->withCommonCalendar();
$duration = $duration->withJulianCalendar();
$duration = $duration->withGregorianCalendar();
$duration = $duration->withAnomalisticCalendar();
$duration = $duration->withSiderealCalendar();
$duration = $duration->withTropicalCalendar();
$duration = $duration->withDraconicCalendar();
```

## PHP DateTime Conversion

`Duration` can be converted to and from PHP's `DateTime`, which is very useful for applications that need to handle
timestamps or date and time.

### DateInterval

You can convert from DateInterval to Duration, or parse DateInterval strings.

```php
$duration = Duration::fromDateInterval($interval);

// Same AS

$duration = $duration->withFromDateInterval($interval);

// ---

$duration = Duration::parseDateString('1year 2month 10hours 30minutes 45seconds');

// Same AS

$duration = $duration->withParseDateString('1year 2month 10hours 30minutes 45seconds');
```

Note that `parseDateString()`
uses `DateInterval::createFromDateString()`, so you can use strings like `10hours 30minutes`, but not strings like
`P12DT3H`.
`Duration::parseDateString()` is similar to `Duration::parse()`, but it is specifically for parsing DateInterval
strings, and only supports PHP's built-in date and time formats,
unlike `Duration`, which can support units down to `femtoseconds`, etc.

You can also convert `Duration` to `DateInterval`, but note that PHP's `DateInterval` only supports up to microseconds
precision,
so if you use smaller units (like nanoseconds, femtoseconds, etc.), these units will be rounded to microseconds (the
rounding rule can be customized).

```php
$duration = Duration::parse('350 seconds 300 milliseconds 500 nanoseconds');
$interval = $duration->toDateInterval([$roundingMode = HALF_UP]);

echo $duration->intervalToMicroseconds($interval); // 350300001
```

### Convert to DateTime

`Duration` can convert a duration to a future or past `DateTime`, which is very useful for calculating the time after or
before an event occurs.

```php
// Current date is 2023-10-01 00:00:00

$duration = Duration::parse('5days');

// To: 2023-10-06T00:00:00+00:00
echo $duration->toFutureDateTime()->format(DateTime::ATOM);

// To: 2023-09-26T00:00:00+00:00
echo $duration->toPastDateTime()->format(DateTime::ATOM);
```

You can also specify the starting date

```php
$now = new DateTimeImmutable('2023-10-01T00:00:00');

$datetime = $duration->toPastDateTime($now);

// OR

$datetime = $duration->toPastDateTime('2023-10-01T00:00:00');
```
