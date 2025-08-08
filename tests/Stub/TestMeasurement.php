<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests\Stub;

use Asika\BetterUnits\AbstractBasicMeasurement;

class TestMeasurement extends AbstractBasicMeasurement
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
