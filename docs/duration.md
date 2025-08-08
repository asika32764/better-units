# Duration Measurement

Duration 是一個計算時間長度單位的工具，通常用於測量事件或操作的持續時間。

<!-- TOC -->
* [Duration Measurement](#duration-measurement)
  * [建立](#建立)
  * [可用單位](#可用單位)
  * [轉換](#轉換)
  * [格式化](#格式化)
  * [曆法](#曆法)
    * [可用曆法常數](#可用曆法常數)
    * [快速設定](#快速設定)
  * [PHP DateTime 轉換](#php-datetime-轉換)
    * [DateInterval](#dateinterval)
    * [轉換成 DateTime](#轉換成-datetime)
<!-- TOC -->

## 建立

要建立一個 Duration 實例，可以使用以下方法：

```php
use Asika\BetterUnits\Duration;

$duration = new Duration(3600); // 3600 seconds
$duration = new Duration(100, Duration::UNIT_MINUTES); // 100 minutes

$duration = Duration::parse('2 hours 30 minutes'); // 2 hours and 30 minutes

$duration = Duration::from(300, 'days');
$duration = Duration::from('300 days');
```

## 可用單位

- Atom Unit: `femtoseconds`
- Default Unit: `seconds`
- Base Unit: `seconds`

| 單位             | 常數                  | 別名                                        | 比率（相對秒）             | 說明     |
|----------------|---------------------|-------------------------------------------|---------------------|--------|
| `femtoseconds` | `UNIT_FEMTOSECONDS` | `fs`, `femtosecond`, `femtoseconds`       | `0.000000000000001` | 飛秒     |
| `picoseconds`  | `UNIT_PICOSECONDS`  | `ps`, `picosecond`, `picoseconds`         | `0.000000000001`    | 皮秒     |
| `nanoseconds`  | `UNIT_NANOSECONDS`  | `ns`, `nanosecond`, `nanoseconds`         | `0.000000001`       | 奈秒     |
| `microseconds` | `UNIT_MICROSECONDS` | `μs`, `us`, `microsecond`, `microseconds` | `0.000001`          | 微秒     |
| `milliseconds` | `UNIT_MILLISECONDS` | `ms`, `millisecond`, `milliseconds`       | `0.001`             | 毫秒     |
| `seconds`      | `UNIT_SECONDS`      | `s`, `sec`, `second`, `seconds`           | `1`                 | 秒      |
| `minutes`      | `UNIT_MINUTES`      | `m`, `min`, `minute`, `minutes`           | `60`                | 分鐘     |
| `hours`        | `UNIT_HOURS`        | `h`, `hr`, `hour`, `hours`                | `3600`              | 小時     |
| `days`         | `UNIT_DAYS`         | `d`, `day`, `days`                        | `86400`             | 天      |
| `weeks`        | `UNIT_WEEKS`        | `w`, `week`, `weeks`                      | `604800`            | 週      |
| `months`       | `UNIT_MONTHS`       | `mo`, `month`, `months`                   | `2629440`           | 月（可變動） |
| `years`        | `UNIT_YEARS`        | `y`, `year`, `years`                      | `31536000`          | 年（可變動） |

## 轉換

可使用 `to()` 或 `toXxx()` 方法將 Duration 轉換成其他單位的值：

```php
$duration->toSeconds();
$duration->toMinutes(scale: 2, RoundingMode::HALF_UP);
```

支援的函式

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

## 格式化

Duration 適合用在顯示經歷時長、收聽時間等，如果您將使用者的收聽時間轉成秒數儲存，以下示範了如何轉成人類可讀的顯示方式

```php
$seconds = 465718;
$totalPlaySeconds = Duration::from($seconds, 's');
echo $totalPlaySeconds->humanize(); // 5days 9hours 21minutes 58seconds
```

由於收聽時間我們期望最大單位為小時，我們可以在 `humanize()` 方法中指定印出的單位：

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

如果要顯示成 `24:59:33` 這樣的播放器格式，可以如下設定

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

自行客製化顯示方式，請見 [serializeCallback()](../README.md#serializecallback)

## 曆法

由於人類時間的計算中，年與月並沒有標準長度，而是依照所用的曆法不同而會改變，因此要精確的計算年月時長，必須要指定使用的曆法，才能相對準確。

`Duration` 預設使用 common 曆法，年是用 365 天計算 (`31536000` 秒)，月是用 30 天計算 (`2629440` 秒)。
這適合用在對長時間不敏感的軟體中，例如每月份計算是用量的系統。但對於需要跨越份與年份統計時長的系統來說，就顯得不夠準確。

`Duration` 提供一系列曆法單位與其對應的秒數，可以透過 `withYearSeconds()` 與 `withMonthSeconds()` 方法來設定，
下面示範了設定 Anomalistic 曆法的方式：

```php
$duration = new Duration()
    ->withYearSeconds(Duration::MONTH_SECONDS_ANOMALISTIC)
    ->withMonthSeconds(Duration::MONTH_SECONDS_ANOMALISTIC);

$duration->withParse('1year 2months')
    ->toSeconds(); // BigDecimal(7142139.36)
```

### 可用曆法常數

注意除了 common 以外，所有的年、月秒數都是平均值，非固定值，隨著地球運行，每年都會改變。本套件僅能用平均秒數做計算，
如果要依照年份計算時長，請改用合適的曆法套件做計算。

| 常數                          | 曆法                                                              | 秒數            | 備註          |
|-----------------------------|-----------------------------------------------------------------|---------------|-------------|
| `YEAR_SECONDS_COMMON`       | [Common](https://en.wikipedia.org/wiki/Common_year)             | `31536000`    | 365 天       |
| `YEAR_SECONDS_LEAP`         | [Leap](https://en.wikipedia.org/wiki/Leap_year)                 | `31622400`    | 366 天       |
| `YEAR_SECONDS_GREGORIAN`    | [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar)   | `31556952`    | 平均年長，曆法混合   |
| `YEAR_SECONDS_JULIAN`       | [Julian](https://en.wikipedia.org/wiki/Julian_year_(astronomy)) | `31557600`    | 365.25 天    |
| `YEAR_SECONDS_TROPICAL`     | [Tropical](https://en.wikipedia.org/wiki/Tropical_year)         | `31556925.97` | 回歸年         |
| `YEAR_SECONDS_SIDEREAL`     | [Sidereal](https://en.wikipedia.org/wiki/Sidereal_year)         | `31558149.76` | 恆星年         |
| `YEAR_SECONDS_ANOMALISTIC`  | [Anomalistic](https://en.wikipedia.org/wiki/Anomalistic_year)   | `31558432.55` | 近日點年        |
| `YEAR_SECONDS_DRACONIC`     | [Draconic](https://en.wikipedia.org/wiki/Draconic_year)         | `29947971`    | 交點年         |
| `YEAR_SECONDS_GAUSSIAN`     | [Gaussian](https://en.wikipedia.org/wiki/Gaussian_year)         | `31558196.01` | 高斯年         |
| `MONTH_SECONDS_COMMON`      | [Common](https://en.wikipedia.org/wiki/Common_year)             | `2629440`     | 30.44 天     |
| `MONTH_SECONDS_GREGORIAN`   | [Gregorian](https://en.wikipedia.org/wiki/Gregorian_calendar)   | `2629746`     | 30.436875 天 |
| `MONTH_SECONDS_JULIAN`      | [Julian](https://en.wikipedia.org/wiki/Julian_year_(astronomy)) | `2629800`     | 30.4375 天   |
| `MONTH_SECONDS_TROPICAL`    | [Tropical](https://en.wikipedia.org/wiki/Tropical_month)        | `2360584.51`  | 27.32158 天  |
| `MONTH_SECONDS_SIDEREAL`    | [Sidereal](https://en.wikipedia.org/wiki/Sidereal_month)        | `2360591.5`   | 27.321661 天 |
| `MONTH_SECONDS_ANOMALISTIC` | [Anomalistic](https://en.wikipedia.org/wiki/Anomalistic_month)  | `2380713.12`  | 27.55455 天  |
| `MONTH_SECONDS_DRACONIC`    | [Draconic](https://en.wikipedia.org/wiki/Draconic_month)        | `2351135.81`  | 27.21222 天  |
| `MONTH_SECONDS_28DAYS`      | 固定 28 天                                                         | `2419200`     | 固定 28 天     |
| `MONTH_SECONDS_29DAYS`      | 固定 29 天                                                         | `2505600`     | 固定 29 天     |
| `MONTH_SECONDS_30DAYS`      | 固定 30 天                                                         | `2592000`     | 固定 30 天     |
| `MONTH_SECONDS_31DAYS`      | 固定 31 天                                                         | `2678400`     | 固定 31 天     |

### 快速設定

Duration 提供了快速設定曆法的方法，可以直接使用以下方法來設定，無需分別設定年與月。

```php
$duration = $duration->withCommonCalendar();
$duration = $duration->withJulianCalendar();
$duration = $duration->withGregorianCalendar();
$duration = $duration->withAnomalisticCalendar();
$duration = $duration->withSiderealCalendar();
$duration = $duration->withTropicalCalendar();
$duration = $duration->withDraconicCalendar();
```

## PHP DateTime 轉換

`Duration` 可以與 PHP 的 `DateTime` 互相轉換，這對於需要處理時間戳記或日期時間的應用程式非常有用。

### DateInterval

可以從 DateInterval 轉換成 Duration，或是解析 DateInterval 字串。

```php
$duration = Duration::fromDateInterval($interval);

// Same aAS

$duration = $duration->withFromDateInterval($interval);

// ---

$duration = Duration::parseDateString('1year 2month 10hours 30minutes 45seconds');

// Same AS
$duration = $duration->withParseDateString('1year 2month 10hours 30minutes 45seconds');
```

注意 `parseDateString()`
用的是 `DateInterval::createFromDateString()`，所以可以使用 `10hours 30minutes` 這樣的字串，但不能使用 `P12DT3H` 這樣的字串。
`Duration::parseDateString()` 雖然與 `Duration::parse()` 相似，但它專門用於解析 DateInterval 字串，只支援 PHP 內建的時間日期格式，
不像 `Duration` 可以支援到最低 `fetmoseconds` 的單位等。

您也可以將 `Duration` 轉換成 `DateInterval`，要注意的是，由於 PHP 的 `DateInterval` 只支援到 microseconds 的精度，
所以如果您使用了更小的單位（如奈秒、飛秒等），這些單位將會被四捨五入到 microseconds （進位規則可以自訂）。

```php
$duration = Duration::parse('350 seconds 300 milliseconds 500 nanoseconds');
$interval = $duration->toDateInterval([roundingMode => HALF_UP]);

echo $duration->intervalToMicroseconds($interval); // 350300001
```

### 轉換成 DateTime

`Duration` 可以將持續時間轉換成未來或過去的 `DateTime`，這對於需要計算某個事件發生後或之前的時間非常有用。

```php
// Current date is 2023-10-01 00:00:00

$duration = Duration::parse('5days');

// To: 2023-10-06T00:00:00+00:00
echo $duration->toFutureDateTime()->format(DateTime::ATOM);

// To: 2023-09-26T00:00:00+00:00
echo $duration->toPastDateTime()->format(DateTime::ATOM);
```

也可以指定起始日期

```php
$now = new DateTimeImmutable('2023-10-01T00:00:00');

$datetime = $duration->toPastDateTime($now);

// OR

$datetime = $duration->toPastDateTime('2023-10-01T00:00:00');
```
