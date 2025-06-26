# Better PHP Unit Converter

Better Unit Converter is a modern and intuitive unit conversion tool that allows you to convert between various 
units of measurement. It supports a wide range of categories including length, weight, temperature, volume, and more.

## Installation

```bash
composer require asika/unit-converter
```

## Getting Started

### 建立轉換物件

您可以用以下方式建立 Unit 轉換器，每個 Converter 都有自己的預設單位，舉例來說 `Duration` 的預設單位是 `seconds`，
所以當直接建立 Duration 物件時，輸入的值會儲存成 `seconds` 單位。

您可以即將他轉成其他單位，例如 `minutes` 或 `hours`，Unit Converter 使用 [brick/math](https://github.com/brick/math) 
套件來處理數學運算， 因此返回的會是 `BigDecimal` 物件。

```php
use Asika\UnitConverter\Duration;

$duration = new Duration(600); // 600 seconds

// Get raw value in seconds
$duration->value; // BigDecimal(600)

// Convert to minutes value
$duration->toMinutes(); // BigDecimal(10)

(string) $duration->toMinutes()->minus(2); // "8"
```

您也可以指定初始單位，例如以下範例。單位的只硬可以使用類別自帶常數，或是英文的 minutes or min 等單位縮寫。（詳細可用單位請見個別轉換器的文件）

```php
$duration = new Duration(60, Duration::UNIT_MINUTES); // 10 minutes
$duration = new Duration(60, 'minutes'); // 10 minutes
$duration = new Duration(60, 'min'); // 10 minutes

// Get raw value in minutes
$duration->value; // BigDecimal(60)

// Convert to hour value
$duration->toHours(); // BigDecimal(1)
```

要特別注意，當轉出結果包含小數點時，預設的進位規則是"無條件捨去"。所以當您將秒轉成小時或月時，很可能直接得到 `0` 這個結果。
可以加上精度參數 `scale: int` 來指定小數點後的位數。 另外也可以用 brick/math 的 `roundingMode: enum` 參數來更改進位規則。

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
echo $duration->baseUnit; // "minutes"
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

## 單位轉換

Unit Converter 有兩種方式可以轉換單位，一種是轉換單位後維持轉換器物件，一種是轉換後輸出數值。

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

使用 `convertTo()` 方法可以轉換單位後維持轉換器物件，這樣可以方便的進行連鎖操作，所有針對轉換器進行的內容修改都是 immutable 的，
請一定要用新的變數接起來。同樣的，轉換時也要考慮到小單位轉成大單位時，會損失精度，請視轉換需求手動設定 scale 與 roundingMode。

```php
$seconds = new Duration(600, 's'); // 600 seconds

// Immutable
$minutes = $seconds->convertTo(Duration::UNIT_MINUTES);

// $seconds still 600 seconds
$seconds->value; // BigDecimal(600)
$minutes->value; // BigDecimal(10)
```

### 精度控制

出於安全理由，Unit Converter 在轉換單位時，統一採用 brick/math 的 `RoundingMode::DOWN` 作為預設進位規則，會捨棄掉所有小數位數。
也就是說，即便是 59 秒，轉換成分鐘時，也會變成 0分鐘。假設您是使用 `convertTo()` 方法，則轉換過程所有被捨棄的位數都會移除，造成精度損失。
下面示範了這種情況：

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES) // 0 minutes
    ->convertTo(Duration::UNIT_SECONDS); // 0 seconds

$duration->value; // BigDecimal(0) - All precision lost
```

這是由於轉換過程中，若允許不定長度的小數，則一旦出現無窮位數的小數時，轉換過程會出現未預期的微小精度損失，而工程師可能完全沒有察覺。
當多次轉換數值時，結果可能出現難以預料的差距。因此本套件要求開發者有意識的手動指定精度與進位規則，確保轉或過程中任何的精度損失都是在預料中與控制下的。

若您希望指定精度與進位規則，可以在轉換時加上 `scale` 與 `roundingMode` 參數，這樣可以手動掌握精度損失範圍。

```php
$duration = new Duration(59, 's')
    ->convertTo(Duration::UNIT_MINUTES, 8) // 0.98333333 minutes
    ->convertTo(Duration::UNIT_SECONDS, 8); // 58.9999998 seconds

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

任何可以輸入單位進行轉換、或是可以解析字串的部份，都可以使用這些常數或是字串來表示單位。

## 格式化

轉換器提供幾個函式方便我們顯示格式化字串，這些函式在所有轉換器接可用，我們暫時先用 `Duration` 來示範。

### `format()`

`format()` 用來根據當下的單位進行格式化。

```php
$duration = new Duration(59, 's');

$duration->value; // BigDecimal(59)

$duration->format(); // "59seconds"
```

預設的印出格式會用該單位的原始字串作為後綴並緊貼數值，而第一個參數 `suffix` 可以指定輸出格式的後綴，此參數可以是

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

### `serialize()`

serialize() 類似 humanize() ，但無法客製化格式字串，他會將轉換器轉成一個可序列化的字串，方便存入 DB 或快取。
當取回字串時，可以用 `parse()` 方法將字串轉回轉換器物件。

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



