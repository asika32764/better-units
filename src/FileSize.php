<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

class FileSize extends AbstractUnitConverter
{
    public const string UNIT_BITS = 'bits';
    public const string UNIT_BYTES = 'bytes';
    public const string UNIT_KILOBYTES = 'kilobytes';
    public const string UNIT_KIBIBYTES = 'kibibytes';
    public const string UNIT_MEGABYTES = 'megabytes';
    public const string UNIT_GIGABYTES = 'gigabytes';
    public const string UNIT_TERABYTES = 'terabytes';
    public const string UNIT_PETABYTES = 'petabytes';
    public const string UNIT_EXABYTES = 'exabytes';
    public const string UNIT_ZETTABYTES = 'zettabytes';
    public const string UNIT_YOTTABYTES = 'yottabytes';

    // phpcs:disable
    public string $atomUnit = self::UNIT_BITS;

    public string $defaultUnit = self::UNIT_BYTES;

    protected array $unitExchanges = [
        self::UNIT_BITS => 1,
        self::UNIT_BYTES => 8,
        self::UNIT_KILOBYTES => 8_192.0,
        self::UNIT_KIBIBYTES => 8_192.0,
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
        return match (strtolower($unit)) {
            'b', 'bit' => self::UNIT_BITS,
            'B', 'byte' => self::UNIT_BYTES,
            'kb', 'kilobyte' => self::UNIT_KILOBYTES,
            'mb', 'megabyte' => self::UNIT_MEGABYTES,
            'gb', 'gigabyte' => self::UNIT_GIGABYTES,
            'tb', 'terabyte' => self::UNIT_TERABYTES,
            'pb', 'petabyte' => self::UNIT_PETABYTES,
            'eb', 'exabyte' => self::UNIT_EXABYTES,
            'zb', 'zettabyte' => self::UNIT_ZETTABYTES,
            'yb', 'yottabyte' => self::UNIT_YOTTABYTES,
            default => $unit,
        };
    }
}
