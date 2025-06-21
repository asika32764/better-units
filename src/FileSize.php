<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

class FileSize extends AbstractUnitConverter
{
    public const string UNIT_BITS = 'bits';
    public const string UNIT_BYTES = 'Bytes';
    public const string UNIT_KILOBYTES = 'KB';
    public const string UNIT_MEGABYTES = 'MB';
    public const string UNIT_GIGABYTES = 'GB';
    public const string UNIT_TERABYTES = 'TB';
    public const string UNIT_PETABYTES = 'PB';
    public const string UNIT_EXABYTES = 'EB';
    public const string UNIT_ZETTABYTES = 'ZB';
    public const string UNIT_YOTTABYTES = 'YB';

    // phpcs:disable
    public string $atomUnit = self::UNIT_BITS;

    public string $defaultUnit = self::UNIT_BYTES;

    protected array $unitExchanges = [
        self::UNIT_BITS => 1,
        self::UNIT_BYTES => 8,
        self::UNIT_KILOBYTES => 8_192.0,
        self::UNIT_MEGABYTES => 8_388_608.0,
        self::UNIT_GIGABYTES => 8_589_934_592.0,
        self::UNIT_TERABYTES => 8_796_093_022_208.0,
        self::UNIT_PETABYTES => 9_007_199_254_740_992.0,
        self::UNIT_EXABYTES => 9_223_372_036_854_775_808.0,
        self::UNIT_ZETTABYTES => 9_444_732_965_739_290_427_392.0,
        self::UNIT_YOTTABYTES => 9_671_406_556_917_033_397_649_408.0,
    ];
    // phpcs:enable

    protected function normalizeBaseUnit(string $unit): string
    {
        $unit = match ($unit) {
            'b' => self::UNIT_BITS,
            'B' => self::UNIT_BYTES,
            default => $unit,
        };

        return match (strtolower($unit)) {
            'bit', 'bits' => self::UNIT_BITS,
            'byte', 'bytes' => self::UNIT_BYTES,
            'kb', 'kilobyte', 'kilobytes' => self::UNIT_KILOBYTES,
            'mb', 'megabyte', 'megabytes' => self::UNIT_MEGABYTES,
            'gb', 'gigabyte', 'gigabytes' => self::UNIT_GIGABYTES,
            'tb', 'terabyte', 'terabytes' => self::UNIT_TERABYTES,
            'pb', 'petabyte', 'petabytes' => self::UNIT_PETABYTES,
            'eb', 'exabyte', 'exabytes' => self::UNIT_EXABYTES,
            'zb', 'zettabyte', 'zettabytes' => self::UNIT_ZETTABYTES,
            'yb', 'yottabyte', 'yottabytes' => self::UNIT_YOTTABYTES,
            default => $unit,
        };
    }
}
