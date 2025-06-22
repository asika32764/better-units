<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Compound;

use Asika\UnitConverter\AbstractUnitConverter;
use Asika\UnitConverter\Duration;
use Asika\UnitConverter\Length;

class Speed extends AbstractCompoundUnitConverter
{
    public AbstractUnitConverter $measure {
        get => $this->measure ??= new Length();
    }

    public AbstractUnitConverter $deno {
        get => $this->deno ??= new Duration();
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
