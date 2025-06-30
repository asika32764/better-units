# Better PHP Unit Converter

Better Unit Converter is a modern and intuitive unit conversion tool that allows you to convert between various
units of measurement. It supports a wide range of categories including length, weight, temperature, volume, and more.

<!-- TOC -->
* [Better PHP Unit Converter](#better-php-unit-converter)
  * [Installation](#installation)
  * [Getting Started](#getting-started)
    * [如何使用這個套件](#如何使用這個套件)
    * [How to Create Measurement Object](#how-to-create-measurement-object)
    * [用字串建立](#用字串建立)
  * [單位轉換](#單位轉換)
    * [輸出數值](#輸出數值)
    * [convertTo() 方法](#convertto-方法)
    * [精度控制](#精度控制)
  * [單位](#單位)
  * [格式化](#格式化)
    * [`format()`](#format)
    * [`humanize()`](#humanize)
    * [預設格式化處理器](#預設格式化處理器)
    * [`serialize()`](#serialize)
    * [`serializeCallback()`](#serializecallback)
  * [單位管理](#單位管理)
    * [限縮可用單位](#限縮可用單位)
    * [自訂或新增單位](#自訂或新增單位)
    * [變更換算比率](#變更換算比率)
    * [其他可能的單位調整](#其他可能的單位調整)
  * [取得最接近 1 的單位](#取得最接近-1-的單位)
  * [變更 Measurement 的內容](#變更-measurement-的內容)
    * [運算](#運算)
  * [Compound Measurement](#compound-measurement)
    * [Predefined Units](#predefined-units)
  * [建立自己的 Measurement](#建立自己的-measurement)
    * [動態 Measurement](#動態-measurement)
  * [可用的單位與其文件](#可用的單位與其文件)
<!-- TOC -->

## Installation

```bash
composer require asika/unit-converter
```

## Getting Started

### 如何使用這個套件

這個套件提供了一個簡易直覺化的測量值儲存與轉換功能，您可以用它來儲存與轉換各種單位的測量值，例如時間、長度、重量、溫度等等，
並可以在物件與函式間傳遞。

在以前，當您的物件需要接收一個測量值時，您可能會使用 `int` 或 `float` 來表示這個數值，但這樣會有一些問題：
- 您無法確保這個數值的單位是正確的，可能會出現單位錯誤的情況。
- 您無法確保這個數值的精度，可能會出現精度損失的情況。
- 您無法確保這個數值的範圍，可能會出現數值溢出的情況。
- 您無法確保這個數值的格式，可能會出現格式錯誤的情況。

舉例來說，以下是一個計算收聽總時長的函式，但開發者在使用時，無法得知 `$duration` 參數的單位是什麼，
可能是秒、分鐘、甚至小時，這樣會導致計算錯誤。

```php
function calcListenTime(int $duration): string {
    return sprintf('%.2f hours', $duration / 3600);
}

// What unit is this? Could be seconds, minutes, or hours
calcListenTime(3600);
```

改用本套件的物件作為參數值後，函式本身不需要關注單位的細節，因為轉換器會自動處理單位轉換與精度問題。開發者只要將測量植物件送數入函式即可
，函式會自動轉換成必要的單位與格式。

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
use Asika\UnitConverter\Duration;

$duration = new Duration(600); // 600 seconds

// Get raw value in seconds
$duration->value; // BigDecimal(600)

// Convert to minutes value
$duration->toMinutes(); // BigDecimal(10)

(string) $duration->toMinutes()->minus(2); // "8"
```

您也可以指定初始單位，例如以下範例。單位的指定可以使用類別自帶常數，或是英文的 minutes or min 等單位縮寫。（詳細可用單位請見個別轉換器的文件）

```php
$duration = new Duration(60, Duration::UNIT_MINUTES); // 10 minutes
$duration = new Duration(60, 'minutes'); // 10 minutes
$duration = new Duration(60, 'min'); // 10 minutes

// Get raw value in minutes
$duration->value; // BigDecimal(60)

// Convert to hour value
$duration->toHours(); // BigDecimal(1)
```

要特別注意，當轉出結果包含小數點時，預設的進位規則是"無條件捨去"。所以當您將較小的單位轉成較大的單位，但數值不足以進位時，
例如將秒轉成小時或月時，很可能直接得到 `0` 這個結果，這是預期內的行為。

您可以加上精度參數 `scale: int` 來指定小數點後的位數。 另外也可以用 brick/math 的 `roundingMode: enum` 參數來更改進位規則。

```php
$duration->toHours(); // BigDecimal(0)
$duration->toHours(scale: 5); // BigDecimal(0.16666)

$duration->toHours(scale: 1, roundingMode: \Brick\Math\RoundingMode::HALF_UP); // BigDecimal(0.17)
```

### 用字串建立

Unit Convert 也支援用字串建立轉換物件，這樣可以更方便的處理不同單位之間的轉換。但要注意的是，採用字串分析單位值時，會以該單位所支援的最小
原子單位來分析，然後再轉換成預設單位。舉例來說，`Duration` 的最小原子單位是 `femtoseconds`，預設單位是 `seconds`，
所以當您用字串建立 Duration 時，會將該字串解析成 `femtoseconds`，然後再轉換成 `seconds`。這會使得您獲得的原始數值包含小數點，
這樣可以原封不動的保留完整數值，後續會在說明如何進行數值的轉換與正規化。

```php
$duration = \Asika\UnitConverter\Duration::parse('10hours 5minutes 30seconds 50ms 100ns 300fs');

$duration->value; // BigDecimal(36330.0500001000003)
```

如果您希望解析字串後，轉成另一個單位，可以加上第二個 asUnit 參數

```php
$duration = Duration::parse(
    '10hours 5minutes 30seconds 50ms 100ns 300fs',
    asUnit: Duration::UNIT_MINUTES
)->value; // BigDecimal(605.500833335000005)

// Peek the current unit of this converter
echo $duration->unit; // "minutes"
```

同樣的，所有包含單位轉換的功能，都可以加上 `scale` 與 `roundingMode` 參數來控制小數點後的位數與進位規則。

```php
$duration = Duration::parse(
    '10hours 5minutes 30seconds 50ms 100ns 300fs',
    asUnit: Duration::UNIT_MINUTES,
    scale: 3,
    roundingMode: \Brick\Math\RoundingMode::HALF_UP
)->value; // BigDecimal(605.501)
```

`from()` 則是較為泛用的功能，如果送入字串作為參數，就會進行字串解析，如果送入數字，就會直接作為測量值建立物件：

```php
$duration = Duration::from('100seconds');
$duration = Duration::from('3 years 50days 5hours 30minutes', scale: 4, roundingMode: RoundingMode::HALF_UP);
$duration = Duration::from(1200); // 1200 seconds
$duration = Duration::from(500, Duration::UNIT_MINUTES);
```

## 單位轉換

Unit Converter 有兩種方式可以轉換單位，一種是轉換單位後維持 Measurement 物件，一種是轉換後輸出數值。

### 輸出數值

使用 `to()` or `toXxx()` 方法可以轉換單位後輸出數值，所有數值都會是 `BigDecimal` 物件。

```php
$duration->to(Duration::UNIT_MINUTES); // Use constants
$duration->to('months'); // Months
$duration->to('y'); // Year with shortcut

// Or preset methods
$duration->toMinutes(); // Minutes
$duration->toHours(); // Hours
$duration->toDays(); // Days
```

果只是要取得當下的數值，可以直接用 `value` 屬性來取得，這樣會直接返回 `BigDecimal` 物件。

```php
$duration->value; // BigDecimal(600)
```

### convertTo() 方法

使用 `convertTo()` 方法可以轉換單位後維持 Measurement 物件，這樣可以方便的進行連鎖操作，所有針對 Measurement 進行的內容修改都是
immutable 的，請一定要用新的變數接起來。同樣的，轉換時也要考慮到小單位轉成大單位時，會損失精度，
請視轉換需求手動設定 scale 與 roundingMode。

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

### 精度控制

出於安全理由，Unit Converter 在轉換單位時，統一採用 brick/math 的 `RoundingMode::DOWN` 作為預設進位規則，會捨棄掉所有小數位數。
也就是說，即便是 59 秒，轉換成分鐘時，也會變成 0 分鐘。假設您是使用 `convertTo()` 方法，則轉換過程所有被捨棄的位數都會移除而無法還原，
造成精度損失。下面示範了這種情況：

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES) // 0 minutes
    ->convertTo(Duration::UNIT_SECONDS); // 0 seconds

$duration->value; // BigDecimal(0) - All precision lost
```

這是由於轉換過程中，若允許不定長度的小數，則一旦出現無窮位數的小數時，轉換過程會出現未預期的微小精度損失，而工程師或使用者可能完全沒有察覺。
一旦多次轉換數值時，結果可能出現難以預料的差距。因此本套件要求開發者有意識的手動指定精度與進位規則，確保轉或過程中任何的精度損失都是在預料中與控制下的。

若您希望指定精度與進位規則，可以在轉換時加上 `scale` 與 `roundingMode` 參數，這樣可以手動掌握精度損失範圍。

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, scale: 8) // 0.98333333 minutes
    ->convertTo(Duration::UNIT_SECONDS, scale: 8); // 58.9999998 seconds

// Back to seconds
$duration->value; // BigDecimal(58.9999998)
```

下面示範在轉換為大單位時使用高精度，轉換為小單位時使用 half up，這樣便能將秒數還原回原本的數值

```php
new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, 8, RoundingMode::HALF_UP) // 0.98333333 minutes
    ->convertTo(Duration::UNIT_SECONDS, 0, RoundingMode::HALF_UP);

// BigDecimal(59)
```

## 單位

Unit Converter 可以用常數或是英文單位字串來表達單位，以 `Duration` 為例，支援的單位有：

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

任何可以輸入單位進行轉換、或是可以解析字串的部份，都可以使用這些常數或是字串來表示單位，單位與數值之間有無空格都沒問題，
例如 `2hours`、`2 hours`、`2hr`、`2 hr` 都是可以接受的格式，根據不同的 Measurement，單複數如 `year` `years` 通常也通用
(某些單位因為單複數有特別差異時便無法通用，依照該 Measurement 為準)。

下面是解析時的輸入範例:

```php
\Asika\UnitConverter\Duration::parse('10 hours 5 minutes 30 seconds 50ms 100ns 300fs');
\Asika\UnitConverter\Duration::parse('3y 2mo 1w 2d 3h 4min 5s 6ms 7μs 8ns 9fs');
```

## 格式化

Measurement 提供幾個函式方便我們顯示格式化字串，這些函式在所有 Measurement 大多可用，我們暫時先用 `Duration` 來示範。

### `format()`

`format()` 用來根據當下的單位進行格式化，預設的印出格式會用該單位的原始字串作為後綴並緊貼數值。

```php
$duration = new Duration(59, 's');

$duration->value; // BigDecimal(59)

$duration->format(); // "59seconds"
```

而第一個參數 `suffix` 可以指定輸出格式的後綴，此參數可以是:

- 純字串，作為後綴
- 包含 `%s` 的字串，會作為 `sprintf` 模版
- `Closure` 會在執行時傳入數值與物件，並返回字串，使合用在整合框架 i18n 等
    - 格式: `Closure(BigDecimal $value, string $unit, AbstractConverter $converter): string`

```php
$duration->format(); // "59seconds"

$duration->format(suffix: ' SEC'); // "59 SEC"

// Use for localization, this is "seconds" in Chinese. 
$duration->format(suffix: '秒'); // "59秒"

// Use template string
$duration->format(suffix: 'The Timeout is: %s'); // "The Timeout is: 59"

// Closure
$duration->format(
    function (BigDecimal $value, string $unit, AbstractConverter $converter): string {
        // Integrate with i18n or other frameworks
        return Translator::trans('app.timeout.unit.seconds', value: $value->toScale(2), unit: $unit);
    }
); // "Timeout: 59.00 seconds"
```

`format()` 可以指定輸出單位，會在當下執行立即轉換後印出。由於是格式化為單一單位，因此同樣要考慮到精度與進位規則。

```php
$duration = new Duration(59, 's');

$duration->format(unit: Duration::UNIT_MINUTES); // 0minutes

$duration->format(unit: Duration::UNIT_MINUTES, scale: 8); // "0.98333333minutes"
```

如果您在解析數值或使用 `convertTo()` 轉換單位時，已經設定好精度與進位規則，則呼叫 `format()` 時不用加上 scale，
將會使用當下設定的精度來顯示。

```php
new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, scale: 8) // The scale will save into the converter
    ->format(); // "0.98333333minutes"
```

### `humanize()`

`humanize()` 用來將當前單位轉換成更易讀的格式，會自動切割每個單位從大到小顯示。

```php
$duration = Duration::parse('162231024996102500ns');

echo $duration->humanize(); 
// 5years 1month 3weeks 1day 5hours 46minutes 24seconds 996milliseconds 102microseconds 500nanoseconds
```

這很適合用在向末端客戶展示最終統計結果，以下是一個示範，我們向末端客戶顯示本月的音樂播放時間記錄：

```php
$seconds = 465718;
$totalPlaySeconds = Duration::from($seconds, 's');
echo $totalPlaySeconds->humanize(); // 5days 9hours 21minutes 58seconds
```

第一個參數 `formats` 如果送入 Closure ，可以控制所有單位的格式化邏輯，同樣適合用在整合框架 i18n 等，
第二個參數 `divider` 可以控制單位之間的分隔符號，預設是空格。

```php
$totalPlaySeconds->humanize(
    formats: fn(BigDecimal $value, string $unit) => $value . ' ' . strtoupper($unit),
    divider: ' / '
);
// 5 DAYS / 9 HOURS / 21 MINUTES / 58 SECONDS
```

但我們通常只需要顯示到小時，不需要將小時轉換成 days，我們可以提供單位陣列給第一個參數 `formats` 控制想要顯示的單位，

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

如果 `formats` 是陣列的話，也可以提供格式化用的 Closure，以下我們進一步使用簡化的時間表達式，適合用在顯示影音播放器時長：

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

另外 `humanize()` 最後一個參數 `options` 具有兩個指令可用

`OPTION_NO_FALLBACK` 控制當總計數值為 0 時，是否顯示 0 單位

```php
$duration = new Duration(0, 's');

echo $duration->humanize(); // "0seconds"
echo $duration->humanize(options: Duration::OPTION_NO_FALLBACK); // ""
```

`OPTION_KEEP_ZERO` 控制當某單位數值為 0 時，是否顯示出來

```php
$duration = new Duration(1000500, 's');
echo $duration->humanize(); // "1week 4days 13hours 55minutes"

echo $duration->humanize(options: Duration::OPTION_KEEP_ZERO);
// 0years 0months 1week 4days 13hours 55minutes 0seconds 0milliseconds
// 0microseconds 0nanoseconds 0picoseconds 0femtoseconds
```

### 預設格式化處理器

Converter 可以註冊一個預設的格式化處理器，當 `format()` 或 `humanize()` 沒有指定格式化參數時，會使用這個處理器。
下面示範了一個根據複數與否，改變 suffix 型態的處理器。注意 suffixFormatter 與 format() 處理器參數不同，
第一個參數是預設採用的 suffix，第二個參數是單位數值，可以用這兩者進行必要的判斷。

```php
$converter = $converter->withSuffixFormatter(
    function (string $suffix, BigDecimal $value, string $unit, Duration $converter): string {
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

serialize() 類似 humanize() ，但無法客製化格式字串，他會將 Measurement 轉成一個可序列化的字串，方便存入 DB 或快取。
當取回字串時，可以用 `parse()` 方法將字串轉回 Measurement 物件。

```php
$duration = new Duration(1000500, 's');
$serialized = echo $duration->serialize(); // 1week 4days 13hours 55minutes

$newDuration = Duration::parse($serialized);

$duration->value->equals($newDuration->value); // TRUE
```

serialize() 也可以指定輸出單位，這樣可以在序列化時就轉成特定單位。

```php
$duration = new Duration(1000500, 's');
echo $duration->serialize(
    [
        Duration::UNIT_HOURS,
        Duration::UNIT_MINUTES,
    ]
); // 277hours 55minutes
```

要注意的是 serialize() 不支援小數點，盡量以可表達的最小單位做序列化，以免精度遺失

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

`serializeCallback()` 是一個強大的工具，您可以依據您需要的邏輯，自行編寫格式化字串、整合框架翻譯或是配合應用顯示合理數據等等。

這個函式會傳入一個 `Closure`，該 `Closure` 接受2個參數： 
`Closure(AbstractConverter $remainder, array<string, BigDecimal> $sortedUnits): string` ，第一個參數是已經轉成 atomUnit 
的 Measurement 物件，第二個參數是已經根據轉換率排序好的單位與數值陣列。

以下我們同樣用 Duration 來示範:

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

`$sortedUnits` 是一個經過排序的陣列，依照單位比例由大到小排列，因此我們可以從最大的單位開始提取，當餘下不足以提取的數值，
就會交由小一級的單位來提取，一直到最小的原子單位為止。所有提取的數值都會是整數，因為除不盡的數值會被保留下來給下一個單位提取，
所以您不需要煩惱 format 時的精度問題。

`withExtract()` 方法會從 Measurement 中提取出指定單位的數值，存成一個 tuple `[extracted, remainder]`，
假設我們最大的單位是 `year` ，他會嘗試提取整數的 year 出來成為一個獨立的 Measurement 物件稱為 `extracted`， 剩餘除不盡的數值會是 `remainder`。
`remainder` 被交給下一個迴圈的 months 繼續提取。直到 `remainder` 為 0 或所有單位跑完之後停止。

由於 `withExtract()` 強大的提取能力，我們完全可以自訂想要序列化的單位清單，不見得要連號 (但要自行控制好單位大小順序)。

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

但要注意，如果您的 Measurement 當下單位小於您序列化的最小單位，則會出現精度損失的情況，因為 `withExtract()` 只會提取整數部分，
剩餘的小數部分會被捨棄。或者您需要自行將最後一個 remainder 輸出成小數字串。

## 單位管理

每個 Measurement 都有一些單位設定，我們做一個簡單介紹:

- `$converter->atomUnit`: Measurement 的最小原子單位，通常是該 Measurement 最小的不可分割單位，例如 `Duration` 的 `femtoseconds`。
- `$converter->baseUnit`: 單位交換比率的基準單位，是該 Measurement 比率為 `1` 的單位，例如 `Duration` 的 `seconds`。
- `$converter->defaultUnit`: 當建立 Measurement 時，若沒有指定單位，則會使用這個單位作為預設單位，通常等於 `baseUnit` 但不一定會一樣。例如 `Duration` 的 `seconds`。
- `$converter->unit`: 當前 Measurement 的單位，可以在建立時手動指定，或是透過 `convertTo()` 方法來改變。

當使用 `parse()` 方法解析字串時，所有 Measurement 自動將字串轉換成 `atomUnit` 的數值，然後再轉換成 `defaultUnit` 或指定的單位的數值。

### 限縮可用單位

有時候，我們不希望 Measurement 處理所有的單位，舉例來說，您可能希望 `Duration` 忽略 `weeks` 單位，或是希望 `FileSize` 僅使用所有 bytes 為基礎的單位。

您可以使用 `withAvailableUnits()` 方法來限制可用的單位，這樣在轉換與輸出時就只能使用這些單位。

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

個別 Measurement 的常用單位可以參考各自的文件，或是直接查看 Measurement 類別的常數定義。

### 自訂或新增單位

Measurement 支援自訂或新增單位，您可以透過 `withAddedUnitExchangeRate()` 方法來新增一個新的單位，這個單位會被加入到 Measurement 的可用單位列表中。
單位的 rate 是以該 Measurement 的設定為 `1` 的單位為基準，例如 `Duration` 的 `seconds` 是比率為 `1` 的基準單位。
我們可以嘗試新增一個 `centuries` 單位，並且將其比率設定為 `3153600000` 秒 (即 100 年的秒數)。

```php
$duration = new Duration()->withAddedUnitExchangeRate('centuries', 3_153_600_000);

$duration->withParse('350years')
    ->format(unit: 'centuries', scale: 1); // "3.5centuries"
```

為了讓 `centuries` 單位能夠認得各種簡寫，我們可以加上 `withUnitNormalizer()` 方法來設定單位的正規化器，這樣可以讓 `centuries` 
支援 `century`、`c` 等簡寫。這個正規化器是額外附加的，不會覆蓋內建單位的行為。

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

如果您希望 Measurement 可以被序列化，您可以使用 callable 指向靜態函式作為 normalizer，這樣可以避免 Closure 無法被序列化的問題 
(或者您也可以考慮使用 [laravel/serializable-closure](https://github.com/laravel/serializable-closure))。

```php
$duration = $duration->withUnitNormalizer(
    [MyCenturiesHelper::class, 'normalizeUnit'] // 靜態函式 normalizeUnit
);
```

如果您要動態設定 `centuries` 的秒數，可以用任何您想要的單位來做換算，例如我們以 year 的比率來換算

```php
$duration = new Duration();
$yearRate = $duration->getUnitExchangeRate(Duration::UNIT_YEARS);
$duration = $duration->withAddedUnitExchangeRate(
    'centuries',
     $yearRate->multipliedBy(100)
);
```

> 注意這個數值是近似值，實際上 1 年的秒數根據曆法可能會有所不同，詳細請見 Duration 的文件。

### 變更換算比率

每個 Measurement 都有不同的作為 `1` 的基準單位，例如 Duration 的基準單位是 `seconds`，而 FileSize 的基準單位是 `bytes`。

Duration 的 unitExchanges 像這樣:

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

出於某些原因，假設您需要更改基準單位的換算比率，您可以使用 `withUnitExchangeRate()` 方法來設定新的基準單位比率。
下面是一個示範，將 femtoseconds 的比率設定為 `1`，成為新的基準單位。這個函式會重設所有可用單位，因此同時您也可以增減自己想要的單位，

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

由於後續的比率可能會超過整數上限，建議要轉成字串或浮點數呈現。 unitExchanges 可以接受 int | float | string | BigDecimal 等格式，
之後會統一轉成 BigDecimal 方便後續計算。

此函式要求強制重新指定 `atomUnit` 與 `defaultUnit`，這是因為 Measurement 的單位與比率是緊密相關的。
`defaultUnit` 不一定要與 `baseUnit` 相同，這是用在建立 Measurement 時若沒有指定單位，預設的基礎單位。

您也可以單純用 `withAddedUnitExchangeRate()` 方法來新增單位，或是用 `withoutUnitExchangeRate()` 方法來移除單位，而不會影響到現有的單位。

### 其他可能的單位調整

每種不同的測量值單位，都有屬於他們的設定，這些設定可以用來調整單位的行為或計算邏輯。
舉例來說， `Duration` 可設定曆法規則，用於計算每年與每月的秒數。

```php
$duration = new \Asika\UnitConverter\Duration();
$duration = $duration->withAnomalisticCalendar(); // Use Anomalistic Calendar for year/month calculations

// you must parse values after setting calendar
$duration->withParse('1 year')->toSeconds(); // 31556952 seconds (Anomalistic year)
```

或者 `FileSize` 因為支援 IS 與 IEC 的單位標準，因此可以設定使用哪一種標準來計算單位。

```php
$fs = new \Asika\UnitConverter\FileSize();
$fs = $fs->withOnlyBytesBinary(); // Use only binary bytes (IEC) for calculations (KiB, MiB, GiB, etc.)

$fs->withParse('100KiB'); // OK
$fs->withParse('100KB'); // ERROR: Unknown base unit: KB
```

更多詳細設定方式，請參見每個測量單位各自的文件。

## 取得最接近 1 的單位

Measurement 提供了 `nearest()` 方法來取得最接近 1 的單位，這個方法會根據當前的數值與單位比率來計算出最接近 1 的單位，
適合用在提供人類易讀的單位顯示時。

```php
$fs = \Asika\UnitConverter\FileSize::from('8500KiB');
$nearest = $fs->nearest(scale: 2, RoundingMode::HALF_UP)->format(); // 8.31MiB
```

## 變更 Measurement 的內容

Measurement 物件是不可變的，這意味著當您對 Measurement 進行任何操作時，都會返回一個新的 Measurement 物件，而不會修改原有的物件。

我們提供一系列方法來變更 Measurement 的內容，若您要變更 Measurement 的值與單位，可以用 `with()`，
這會在不進行任何轉換的情況下，變更值與單位。

```php
$measurement = $measurement->with(100, 'seconds'); // Returns a new Measurement with 100 seconds
```

如果您送入一個具有 scale 的 BigDecimal ，則 Measurement 物件會保留這個 scale，這樣在後續的轉換與格式化時，可以保留精度。

```php
$measurement = $measurement->with(BigDecimal::of(100.25), 'hours');

$measurement->format(); // "100.25hours"
```

如果您單純想變更值、保留單位；或者變更單位、保留值，則可以用 `withValue()` 或 `withUnit()` 方法。

```php
$measurement = \Asika\UnitConverter\Duration::from(100, 'seconds');

$measurement->withValue(300); // Returns a new Duration with 300 seconds, keep unit as seconds
$measurement->withUnit(Duration::UNIT_HOURS); // Returns a new Duration with unit hours, keep value as 300
```

### 運算

Measurement 本身可以做簡單的加減乘除計算，計算的數值可以是 BigNumber、數字或字串。

```php
$new = $measurement->plus(100); // Returns a new Measurement with value + 100
$new = $measurement->minus(50.0); // Returns a new Measurement with value - 50
$new = $measurement->multipliedBy('2'); // Returns a new Measurement with value * 2
$new = $measurement->dividedBy(BigNumber::of(2)); // Returns a new Measurement with value / 2
```

`plus()` 與 `minus()` 可以接受另一個 Measurement 物件來做計算，會自動轉換單位配合原本的 Measurement ，但同樣必須手動指定精度以避免轉換後損失精度，
而加減時預設的 roundingMode 是 `UNNECESSARY`，所以需要盡可能顯式指定 RoundingMode 以避免錯誤發生。

```php
$measurement = new Duration(120, 'seconds'); // 120 seconds
$new = $measurement->plus(new Duration(2, 'minutes'), scale: 2, RoundingMode::HALF_UP); // Returns a new Duration with 240 seconds
$new = $measurement->minus(new Duration(2500, 'ms'), scale: 2, RoundingMode::HALF_UP); // Returns a new Duration with 117.5 seconds
```

如果您要使用更複雜的計算，可以直接取用 `value` 屬性，這是一個 `BigDecimal` 物件，您可以使用 `BigDecimal` 的方法來進行計算。
然後以 `with()` 或 `withValue()` 方法來建立新的 Measurement 物件。這兩個方法也接受 `Closure` 作為參數，這樣可以更靈活地處理計算。

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

有些 Measurement 需要組合多個單位來表示，分別稱作 num (numerator) 與 deno (denominator)，代表分子與分母的單位。

例如 Speed 需要同時表示距離與時間，因此他是一個 Compound Measurement，由 `Length` (measure) + `Duration` (deno) 組合而成。
在表達 `Speed` 的單位時，會是 `Length` 的單位除以 `Duration` 的單位，例如 `m/s` 或 `km/h`。

```php
$speed = Speed::from('100 km/h'); // 100 kilometers per hour
$speed->convertTo('m/s', scale: 4); // 27.7777m/s
```

### Predefined Units

每個 Compound Measurement 都有一些預定義的單位，這些單位可能是國際常用標準單位的命名，例如

- `kph` (km/h, kilometers per hour)
- `mph` (miles per hour)
- `mps` (m/s, meters per second)
- `knots` (knots, nautical miles per hour)

這些單位可以直接用在 `from()` 方法或是 `convertTo()` 方法中，這樣可以方便的建立或轉換 Compound Measurement。

```php
$speed = Speed::from('100 kph'); // 100 kilometers per hour
$speed->convertTo('mps', scale: 4); // 27.7777m/s
```

## 建立自己的 Measurement

以下是一個簡易的示範，建立 Measurement 需要繼承自 `AbstractBasicMeasurement` 類別，其中
必要的繼承屬性有三個，分別是 `$atomUnit` 是最小原子單位，`$defaultUnit` 是預設單位，`$unitExchanges` 是單位換算比率，
請一定至少要有一個 `1` 的基準單位，否則某些運算會出錯。

另外 `normalizeUnit()` 方法是可選的，用來將輸入的單位字串轉換成支援的單位，這個方法會在解析字串或是轉換單位時被呼叫。

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

### 動態 Measurement

您也可以使用 `DynamicMeasurement` 來建立一個動態的 Measurement，這個 Measurement 可以在運行時動態設定單位與比率。

下面示範一個動態貨幣轉換的 Measurement，您可以在運行時設定不同的貨幣與匯率，適合用在電子商務系統。

```php
use Asika\UnitConverter\DynamicMeasurement;

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

## 可用的單位與其文件

- Basic Measurement
    - [Area]()
    - [Duration]()
    - [Energy]()
    - [FileSize]()
    - [Length]()
    - [Volume]()
    - [Weight]()
- Compound Measurement
    - [Speed]()
    - [Bitrate]()




