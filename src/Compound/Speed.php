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
    // public const string UNIT_KNOTS = 'knots';

    public AbstractConverter $measure {
        get => $this->measure ??= new Length();
    }

    public AbstractConverter $deno {
        get => $this->deno ??= new Duration()
            ->withSuffixFormatter(
                fn(string $unit): string => match (strtolower($unit)) {
                    Duration::UNIT_NANOSECONDS => 'ns',
                    Duration::UNIT_MICROSECONDS => 'μs',
                    Duration::UNIT_MILLISECONDS => 'ms',
                    Duration::UNIT_SECONDS => 's',
                    Duration::UNIT_MINUTES => 'min',
                    Duration::UNIT_HOURS => 'h',
                    Duration::UNIT_DAYS => 'd',
                    Duration::UNIT_WEEKS => 'w',
                    Duration::UNIT_MONTHS => 'mo',
                    Duration::UNIT_YEARS => 'y',
                    default => $unit,
                }
            );
    }

    protected function normalizeCompoundUnit(string $unit): string
    {
        return match (strtolower($unit)) {
            'mps' => 'm/s',
            'kph' => 'km/s',
            'mph' => 'km/h',
            default => $unit,
        };
    }
}
