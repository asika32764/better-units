<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Compound;

use Asika\UnitConverter\AbstractConverter;
use Asika\UnitConverter\Duration;
use Asika\UnitConverter\Length;

class Speed extends AbstractCompoundConverter
{
    public const string UNIT_MPS = 'mps';
    public const string UNIT_KPH = 'kph';
    public const string UNIT_MPH = 'mph';
    public const string UNIT_KNOTS = 'knots';

    public string $atomUnit = 'm/s';

    public string $defaultUnit = 'm/s';

    public AbstractConverter $measure {
        get => $this->measure ??= new Length();
    }

    protected array $compoundUnitExchanges = [
        'm/s' => 1,
        self::UNIT_MPH => 0.44704,
        self::UNIT_KNOTS => 0.514444444,
    ];

    public AbstractConverter $deno {
        get => $this->deno ??= new Duration()
            ->withShortUnitFormatters();
    }

    protected function normalizeCompoundUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'mps' => 'm/s',
            'kph' => 'km/h',
            default => $unit,
        };
    }
}
