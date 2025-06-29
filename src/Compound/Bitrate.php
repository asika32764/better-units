<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Compound;

use Asika\UnitConverter\AbstractMeasurement;
use Asika\UnitConverter\Duration;
use Asika\UnitConverter\FileSize;

class Bitrate extends AbstractCompoundMeasurement
{
    public const string UNIT_BITS_PER_SECOND = 'bits/s';

    public const string UNIT_BYTES_PER_SECOND = 'bytes/s';

    public const string UNIT_KBPS = 'Kbps';

    public const string UNIT_MBPS = 'Mbps';

    public const string UNIT_GBPS = 'Gbps';

    public const string UNIT_TBPS = 'Tbps';

    public const string UNIT_KIBPS = 'Kibps';

    public const string UNIT_MIBPS = 'Mibps';

    public const string UNIT_GIBPS = 'Gibps';

    public const string UNIT_TIBPS = 'Tibps';

    public AbstractMeasurement $measure {
        get => $this->measure ??= new FileSize();
    }

    public AbstractMeasurement $deno {
        get => $this->deno ??= new Duration()
            ->withShortUnitFormatters();
    }

    public string $atomUnit = self::UNIT_BITS_PER_SECOND;

    public string $defaultUnit = self::UNIT_BYTES_PER_SECOND;

    protected array $compoundUnitExchanges = [
        self::UNIT_BITS_PER_SECOND => 1.0,
        self::UNIT_BYTES_PER_SECOND => 8.0,
        self::UNIT_KBPS => 1000.0,
        self::UNIT_MBPS => 1000_000.0,
        self::UNIT_GBPS => 1_000_000_000.0,
        self::UNIT_TBPS => 1_000_000_000_000.0,
        self::UNIT_KIBPS => 1024.0,
        self::UNIT_MIBPS => 1_048_576.0,
        self::UNIT_GIBPS => 1_073_741_824.0,
        self::UNIT_TIBPS => 1_099_511_627_776.0,
    ];

    protected function normalizeCompoundUnit(string $unit): string
    {
        return $unit;
    }
}
