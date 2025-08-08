<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Compound;

use Asika\BetterUnits\AbstractMeasurement;
use Asika\BetterUnits\Duration;
use Asika\BetterUnits\Length;

class Speed extends AbstractCompoundMeasurement
{
    public const string UNIT_MPS = 'mps';
    public const string UNIT_KPH = 'kph';
    public const string UNIT_MPH = 'mph';
    public const string UNIT_KNOTS = 'knots';

    public string $atomUnit = 'm/s';

    public string $defaultUnit = 'm/s';

    public AbstractMeasurement $num {
        get => $this->num ??= new Length();
    }

    protected array $compoundUnitExchanges = [
        'm/s' => 1,
        self::UNIT_MPH => 0.44704,
        self::UNIT_KNOTS => 0.514444444,
    ];

    public AbstractMeasurement $deno {
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
