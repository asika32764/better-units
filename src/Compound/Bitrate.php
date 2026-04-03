<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Compound;

use Asika\BetterUnits\AbstractMeasurement;
use Asika\BetterUnits\Duration;
use Asika\BetterUnits\FileSize;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

/**
 * @method BigDecimal toKbps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toMbps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toGbps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toTbps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toKibps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toMibps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toGibps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toTibps(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toBitsPerSecond(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 * @method BigDecimal toBytesPerSecond(?int $scale = null, RoundingMode $roundingMode = RoundingMode::Down)
 */
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

    public AbstractMeasurement $num {
        get => $this->num ??= new FileSize();
    }

    public AbstractMeasurement $deno {
        get => $this->deno ??= new Duration()
            ->withShortUnitFormatters();
    }

    public string $atomUnit = self::UNIT_BITS_PER_SECOND;

    public string $defaultUnit = self::UNIT_BYTES_PER_SECOND;

    protected array $compoundUnitExchanges = [
        self::UNIT_BITS_PER_SECOND => '1',
        self::UNIT_BYTES_PER_SECOND => '8',
        self::UNIT_KBPS => '1000',
        self::UNIT_MBPS => '1000000',
        self::UNIT_GBPS => '1000000000',
        self::UNIT_TBPS => '1000000000000',
        self::UNIT_KIBPS => '1024',
        self::UNIT_MIBPS => '1048576',
        self::UNIT_GIBPS => '1073741824',
        self::UNIT_TIBPS => '1099511627776',
    ];

    protected function normalizeCompoundUnit(string $unit): string
    {
        return $unit;
    }
}
