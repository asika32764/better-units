<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toBits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilobits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKibibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMegabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMebibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGigabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGibibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTerabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTebibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPetabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPebibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toExabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toExbibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toZettabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toZebibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYottabits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYobibits(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toBytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKilobytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toKibibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMegabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toMebibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGigabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toGibibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTerabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toTebibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPetabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toPebibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toExabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toExbibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toZettabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toZebibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYottabytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 * @method BigDecimal toYobibytes(?int $scale = null, RoundingMode $roundingMode = RoundingMode::DOWN)
 */
class FileSize extends AbstractUnitConverter
{
    public const string UNIT_BITS = 'b';

    public const string UNIT_KILOBITS = 'Kb';

    public const string UNIT_KIBIBITS = 'Kib';

    public const string UNIT_MEGABITS = 'Mb';

    public const string UNIT_MEBIBITS = 'Mib';

    public const string UNIT_GIGABITS = 'Gb';

    public const string UNIT_GIBIBITS = 'Gib';

    public const string UNIT_TERABITS = 'Tb';

    public const string UNIT_TEBIBITS = 'Tib';

    public const string UNIT_PETABITS = 'Pb';

    public const string UNIT_PEBIBITS = 'Pib';

    public const string UNIT_EXABITS = 'Eb';

    public const string UNIT_EXBIBITS = 'Eib';

    public const string UNIT_ZETTABITS = 'Zb';

    public const string UNIT_ZEBIBITS = 'Zib';

    public const string UNIT_YOTTABITS = 'Yb';

    public const string UNIT_YOBIBITS = 'Yib';

    public const string UNIT_BYTES = 'B';

    public const string UNIT_KILOBYTES = 'KB';

    public const string UNIT_KIBIBYTES = 'KiB';

    public const string UNIT_MEGABYTES = 'MB';

    public const string UNIT_MEBIBYTES = 'MiB';

    public const string UNIT_GIGABYTES = 'GB';

    public const string UNIT_GIBIBYTES = 'GiB';

    public const string UNIT_TERABYTES = 'TB';

    public const string UNIT_TEBIBYTES = 'TiB';

    public const string UNIT_PETABYTES = 'PB';

    public const string UNIT_PEBIBYTES = 'PiB';

    public const string UNIT_EXABYTES = 'EB';

    public const string UNIT_EXBIBYTES = 'EiB';

    public const string UNIT_ZETTABYTES = 'ZB';

    public const string UNIT_ZEBIBYTES = 'ZiB';

    public const string UNIT_YOTTABYTES = 'YB';

    public const string UNIT_YOBIBYTES = 'YiB';

    public const array UNITS_GROUP_BITS_BASE10 = [
        self::UNIT_BITS,
        self::UNIT_KILOBITS,
        self::UNIT_MEGABITS,
        self::UNIT_GIGABITS,
        self::UNIT_TERABITS,
        self::UNIT_PETABITS,
        self::UNIT_EXABITS,
        self::UNIT_ZETTABITS,
        self::UNIT_YOTTABITS,
    ];

    public const array UNITS_GROUP_BITS_BINARY = [
        self::UNIT_BITS,
        self::UNIT_KIBIBITS,
        self::UNIT_MEBIBITS,
        self::UNIT_GIBIBITS,
        self::UNIT_TEBIBITS,
        self::UNIT_PEBIBITS,
        self::UNIT_EXBIBITS,
        self::UNIT_ZEBIBITS,
        self::UNIT_YOBIBITS,
    ];

    public const array UNITS_GROUP_BYTES_BASE10 = [
        self::UNIT_BITS,
        self::UNIT_BYTES,
        self::UNIT_KILOBYTES,
        self::UNIT_MEGABYTES,
        self::UNIT_GIGABYTES,
        self::UNIT_TERABYTES,
        self::UNIT_PETABYTES,
        self::UNIT_EXABYTES,
        self::UNIT_ZETTABYTES,
        self::UNIT_YOTTABYTES,
    ];

    public const array UNITS_GROUP_BYTES_BINARY = [
        self::UNIT_BITS,
        self::UNIT_BYTES,
        self::UNIT_KIBIBYTES,
        self::UNIT_MEBIBYTES,
        self::UNIT_GIBIBYTES,
        self::UNIT_TEBIBYTES,
        self::UNIT_PEBIBYTES,
        self::UNIT_EXBIBYTES,
        self::UNIT_ZEBIBYTES,
        self::UNIT_YOBIBYTES,
    ];

    // phpcs:disable
    public string $atomUnit = self::UNIT_BITS;

    public string $defaultUnit = self::UNIT_BYTES;

    protected array $unitExchanges = [
        self::UNIT_BITS => 1,
        self::UNIT_KILOBITS => 1_000,
        self::UNIT_KIBIBITS => 1_024,
        self::UNIT_MEGABITS => 1_000_000,
        self::UNIT_MEBIBITS => 1_048_576,
        self::UNIT_GIGABITS => 1_000_000_000,
        self::UNIT_GIBIBITS => 1_073_741_824,
        self::UNIT_TERABITS => 1_000_000_000_000,
        self::UNIT_TEBIBITS => 1_099_511_627_776,
        self::UNIT_PETABITS => 1_000_000_000_000_000,
        self::UNIT_PEBIBITS => 1_125_899_906_842_624,
        self::UNIT_EXABITS => 1_000_000_000_000_000_000,
        self::UNIT_EXBIBITS => 1_152_921_504_606_846_976,
        self::UNIT_ZETTABITS => 1_000_000_000_000_000_000_000,
        self::UNIT_ZEBIBITS => 1_180_591_620_717_411_303_424,
        self::UNIT_YOTTABITS => 1_000_000_000_000_000_000_000_000,
        self::UNIT_YOBIBITS => 1_208_925_819_614_629_174_706_176,
        self::UNIT_BYTES => 8,
        self::UNIT_KILOBYTES => 8_000,
        self::UNIT_KIBIBYTES => 8_192,
        self::UNIT_MEGABYTES => 8_000_000,
        self::UNIT_MEBIBYTES => 8_388_608,
        self::UNIT_GIGABYTES => 8_000_000_000,
        self::UNIT_GIBIBYTES => 8_589_934_592,
        self::UNIT_TERABYTES => 8_000_000_000_000,
        self::UNIT_TEBIBYTES => 8_796_093_022_208,
        self::UNIT_PETABYTES => 8_000_000_000_000_000,
        self::UNIT_PEBIBYTES => 9_007_199_254_740_992,
        self::UNIT_EXABYTES => 8_000_000_000_000_000_000,
        self::UNIT_EXBIBYTES => 9_223_372_036_854_775_808,
        self::UNIT_ZETTABYTES => 8_000_000_000_000_000_000_000,
        self::UNIT_ZEBIBYTES => 9_444_732_965_739_290_427_392,
        self::UNIT_YOTTABYTES => 8_000_000_000_000_000_000_000_000,
        self::UNIT_YOBIBYTES => 9_671_406_556_917_033_397_649_408,
    ];

    // phpcs:enable

    public function withOnlyBitsBinary(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_BITS_BINARY);
    }

    public function withOnlyBitsBase10(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_BITS_BASE10);
    }

    public function withOnlyBytesBinary(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_BYTES_BINARY);
    }

    public function withOnlyBytesBase10(): static
    {
        return $this->withAvailableUnits(self::UNITS_GROUP_BYTES_BASE10);
    }

    protected function normalizeBaseUnit(string $unit): string
    {
        $unit = match ($unit) {
            'b' => self::UNIT_BITS,
            'Kb', 'kb' => self::UNIT_KILOBITS,
            'Kib', 'kib' => self::UNIT_KIBIBITS,
            'Mb', 'mb' => self::UNIT_MEGABITS,
            'Mib', 'mib' => self::UNIT_MEBIBITS,
            'Gb', 'gb' => self::UNIT_GIGABITS,
            'Gib', 'gib' => self::UNIT_GIBIBITS,
            'Tb', 'tb' => self::UNIT_TERABITS,
            'Tib', 'tib' => self::UNIT_TEBIBITS,
            'Pb', 'pb' => self::UNIT_PETABITS,
            'Pib', 'pib' => self::UNIT_PEBIBITS,
            'Eb', 'eb' => self::UNIT_EXABITS,
            'Eib', 'eib' => self::UNIT_EXBIBITS,
            'Zb', 'zb' => self::UNIT_ZETTABITS,
            'Zib', 'zib' => self::UNIT_ZEBIBITS,
            'Yb', 'yb' => self::UNIT_YOTTABITS,
            'Yib', 'yib' => self::UNIT_YOBIBITS,
            'B' => self::UNIT_BYTES,
            'KB' => self::UNIT_KILOBYTES,
            'KiB' => self::UNIT_KIBIBYTES,
            'MB' => self::UNIT_MEGABYTES,
            'MiB' => self::UNIT_MEBIBYTES,
            'GB' => self::UNIT_GIGABYTES,
            'GiB' => self::UNIT_GIBIBYTES,
            'TB' => self::UNIT_TERABYTES,
            'TiB' => self::UNIT_TEBIBYTES,
            'PB' => self::UNIT_PETABYTES,
            'PiB' => self::UNIT_PEBIBYTES,
            'EB' => self::UNIT_EXABYTES,
            'EiB' => self::UNIT_EXBIBYTES,
            'ZB' => self::UNIT_ZETTABYTES,
            'ZiB' => self::UNIT_ZEBIBYTES,
            'YB' => self::UNIT_YOTTABYTES,
            'YiB' => self::UNIT_YOBIBYTES,
            default => $unit,
        };

        // Full name should be case insensitive
        return match (strtolower($unit)) {
            // bits
            'bit', 'bits' => self::UNIT_BITS,
            'kilobit', 'kilobits' => self::UNIT_KILOBITS,
            'kibibit', 'kibibits' => self::UNIT_KIBIBITS,
            'megabit', 'megabits' => self::UNIT_MEGABITS,
            'mebibit', 'mebibits' => self::UNIT_MEBIBITS,
            'gigabit', 'gigabits' => self::UNIT_GIGABITS,
            'gibibit', 'gibibits' => self::UNIT_GIBIBITS,
            'terabit', 'terabits' => self::UNIT_TERABITS,
            'tebibit', 'tebibits' => self::UNIT_TEBIBITS,
            'petabit', 'petabits' => self::UNIT_PETABITS,
            'pebibit', 'pebibits' => self::UNIT_PEBIBITS,
            'exabit', 'exabits' => self::UNIT_EXABITS,
            'exbibit', 'exbibits' => self::UNIT_EXBIBITS,
            'zettabit', 'zettabits' => self::UNIT_ZETTABITS,
            'zebibit', 'zebibits' => self::UNIT_ZEBIBITS,
            'yottabit', 'yottabits' => self::UNIT_YOTTABITS,
            'yobibit', 'yobibits' => self::UNIT_YOBIBITS,

            // Bytes
            'byte', 'bytes' => self::UNIT_BYTES,
            'kilobyte', 'kilobytes' => self::UNIT_KILOBYTES,
            'kibibyte', 'kibibytes' => self::UNIT_KIBIBYTES,
            'megabyte', 'megabytes' => self::UNIT_MEGABYTES,
            'mebibyte', 'mebibytes' => self::UNIT_MEBIBYTES,
            'gigabyte', 'gigabytes' => self::UNIT_GIGABYTES,
            'gibibyte', 'gibibytes' => self::UNIT_GIBIBYTES,
            'terabyte', 'terabytes' => self::UNIT_TERABYTES,
            'tebibyte', 'tebibytes' => self::UNIT_TEBIBYTES,
            'petabyte', 'petabytes' => self::UNIT_PETABYTES,
            'pebibyte', 'pebibytes' => self::UNIT_PEBIBYTES,
            'exabyte', 'exabytes' => self::UNIT_EXABYTES,
            'exbibyte', 'exbibytes' => self::UNIT_EXBIBYTES,
            'zettabyte', 'zettabytes' => self::UNIT_ZETTABYTES,
            'zebibyte', 'zebibytes' => self::UNIT_ZEBIBYTES,
            'yottabyte', 'yottabytes' => self::UNIT_YOTTABYTES,
            'yobibyte', 'yobibytes' => self::UNIT_YOBIBYTES,
            default => $unit,
        };
    }
}
