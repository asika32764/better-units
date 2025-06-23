<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\Area;
use Asika\UnitConverter\Duration;
use Asika\UnitConverter\Energy;
use Asika\UnitConverter\FileSize;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class AbstractUnitConverterTest extends TestCase
{
    public function testFrom(): void
    {
        $d = Duration::from(123456, Duration::UNIT_MINUTES)
            ->convertTo(Duration::UNIT_NANOSECONDS);

        self::assertEquals(
            '7407360000000000nanoseconds',
            $d->format()
        );

        $d = Duration::from('123456minutes')
            ->convertTo(Duration::UNIT_NANOSECONDS);

        self::assertEquals(
            '7407360000000000nanoseconds',
            $d->format()
        );

        $d = Duration::from('123456minutes', Duration::UNIT_NANOSECONDS);

        self::assertEquals(
            '7407360000000000nanoseconds',
            $d->format()
        );

        $d = Duration::from(
            '3year 2month 17days 10hours 3minutes 45seconds 100milliseconds',
            Duration::UNIT_MILLISECONDS
        );

        self::assertEquals(
            '101371905100milliseconds',
            $d->format()
        );

        $d = Duration::from(
            '3y 2mo 17d 10h 3min 45s 100ms',
            Duration::UNIT_MILLISECONDS
        );

        self::assertEquals(
            '101371905100milliseconds',
            $d->format()
        );
    }

    public function testParseToValue(): void
    {
        $d = Duration::parseToValue('123456min', Duration::UNIT_MINUTES);

        self::assertEquals(
            '123456',
            (string) $d
        );

        $d = Duration::parseToValue('123456min', Duration::UNIT_HOURS);

        self::assertEquals(
            '2057.6',
            (string) $d
        );
    }

    #[Test]
    public function withValue(): void
    {
        $d = Duration::from(0, Duration::UNIT_DAYS)
            ->withValue(654321, Duration::UNIT_SECONDS, 3);

        self::assertEquals(
            '7.573days',
            $d->format()
        );

        $d = Duration::from(0, Duration::UNIT_SECONDS)
            ->withValue(4.5, Duration::UNIT_DAYS);

        self::assertEquals(
            '388800seconds',
            $d->format()
        );
    }

    #[Test]
    public function withBaseUnit(): void
    {
        $d = Duration::from(500, Duration::UNIT_SECONDS)
            ->withBaseUnit(Duration::UNIT_DAYS);

        self::assertEquals(
            '500days',
            $d->format()
        );
    }

    #[Test]
    public function with(): void
    {
        $d = Duration::from(123123, Duration::UNIT_SECONDS)
            ->withGregorianCalendar();

        $d2 = $d->with(7.5, Duration::UNIT_DAYS);

        self::assertEquals(
            '7.5days',
            $d2->format()
        );

        // Make sure new object is clone from original object
        self::assertEquals(
            (string) $d->yearSeconds,
            (string) $d2->yearSeconds,
        );

        self::assertNotEquals(
            (string) new Duration()->yearSeconds,
            (string) $d2->yearSeconds,
        );
    }

    #[Test]
    public function format()
    {
        $d = Duration::parse('3years 2months 17days 10hours 3minutes 45seconds 123milliseconds', scale: 5);

        assertEquals(
            '101371905.123seconds',
            $d->format()
        );

        assertEquals(
            '101371905.1seconds',
            $d->format(scale: 1)
        );

        assertEquals(
            '101371905123milliseconds',
            $d->format(unit: Duration::UNIT_MILLISECONDS)
        );

        assertEquals(
            '1689531.75205minutes',
            $d->format(unit: Duration::UNIT_MINUTES)
        );

        assertEquals(
            '1689531.7minutes',
            $d->format(unit: Duration::UNIT_MINUTES, scale: 1)
        );

        assertEquals(
            '1689531.8minutes',
            $d->format(unit: Duration::UNIT_MINUTES, scale: 1, roundingMode: RoundingMode::HALF_UP)
        );

        assertEquals(
            '101371905.1 秒',
            $d->format('%s 秒', scale: 1)
        );

        assertEquals(
            '0000101371905.1 秒',
            $d->format('%015s 秒', scale: 1)
        );

        assertEquals(
            '101371.9K Seconds',
            $d->format(
                fn (BigDecimal $value, string $unit, Duration $d) =>
                    $value->dividedBy(1000, 1, RoundingMode::HALF_UP) . 'K Seconds'
            )
        );
    }

    #[Test]
    #[DataProvider('humanizeProvider')]
    public function humanize(array $args, string $expected): void
    {
        $d = Duration::parse('3years 2months 17days 10hours 3minutes 45seconds 123milliseconds');

        $result = $d->humanize(...$args);

        assertEquals(
            $expected,
            $result
        );
    }

    #[Test]
    public function parse()
    {
        // Parse with whitespace
        $e = Energy::parse('1 gigawatt hour 500j');

        assertEquals(
            '3600000000500j',
            $e->format()
        );

        // Parse if unit contains numbers
        $e = Area::parse('500m2');

        assertEquals(
            '0.000500km2',
            $e->format(unit: Area::UNIT_SQUARE_KILOMETERS, scale: 6)
        );

        // Parse if unit contains special characters
        $e = Area::parse('500m^2');

        assertEquals(
            '0.000500km2',
            $e->format(unit: Area::UNIT_SQUARE_KILOMETERS, scale: 6)
        );

        // Parse if number contains separators
        $e = Area::parse('1,200.05 m^2');

        assertEquals(
            '0.00120005km2',
            $e->format(unit: Area::UNIT_SQUARE_KILOMETERS, scale: 8)
        );
    }

    public static function humanizeProvider(): array
    {
        return [
            'default' => [
                [],
                '3years 2months 2weeks 3days 10hours 3minutes 45seconds 123milliseconds'
            ],
            'h m s' => [
                [
                    [
                        Duration::UNIT_HOURS,
                        Duration::UNIT_MINUTES,
                        Duration::UNIT_SECONDS,
                    ]
                ],
                '28158hours 51minutes 45seconds'
            ],
            'h:m:s' => [
                [
                    [
                        Duration::UNIT_HOURS => $f = fn (BigDecimal $v) => sprintf('%02d', (string) $v),
                        Duration::UNIT_MINUTES => $f,
                        Duration::UNIT_SECONDS => $f,
                    ],
                    ':'
                ],
                '28158:51:45'
            ],
            'format string' => [
                [
                    '%02s',
                    ':'
                ],
                '03:02:02:03:10:03:45:123'
            ],
            'keep zero' => [
                [
                    '%02s',
                    'divider' => ':',
                    'options' => Duration::OPTION_KEEP_ZERO,
                ],
                '03:02:02:03:10:03:45:123:00:00:00:00'
            ],
            'format callback' => [
                [
                    fn (BigDecimal $v, string $unit, Duration $d) => sprintf(
                        '%02d %s',
                        (string) $v,
                        $unit
                    ),
                    ', '
                ],
                '03 years, 02 months, 02 weeks, 03 days, 10 hours, 03 minutes, 45 seconds, 123 milliseconds'
            ],
        ];
    }

    #[Test]
    public function addingUnitAndNormalizer(): void
    {
        $d = Duration::from('350years');
        $d = $d->withAddedUnitExchangeRate(
            'centuries',
            $d->yearSeconds->multipliedBy(100)
        );
        $d = $d->convertTo('centuries', 1);

        assertEquals(
            '3.5centuries',
            $d->format(),
        );

        $d = $d->withUnitNormalizer(
            function (string $unit) {
                if ($unit === 'century' || $unit === 'c') {
                    return 'centuries';
                }

                return $unit;
            }
        )
            ->withSuffixFormatter(
                function (string $suffix, BigDecimal $value) {
                    if ($value->abs()->isEqualTo(1) && $suffix === 'centuries') {
                        $suffix = 'century';
                    }

                    return $suffix;
                }
            )
            ->withParse('1c');

        assertEquals(
            '1century',
            $d->format()
        );
    }

    #[Test]
    public function to()
    {
        $d = Duration::from('350years');

        assertEquals(
            '127750',
            (string) $d->to(Duration::UNIT_DAYS)
        );
    }

    #[Test]
    // nearest
    public function nearest(): void
    {
        $f = FileSize::from('8500KiB');
        $f = $f->nearest(5, units: FileSize::UNITS_GROUP_BYTES_BINARY);

        assertEquals(
            '8.30078MiB',
            $f->format()
        );

        assertEquals(
            FileSize::UNIT_MEBIBYTES,
            $f->baseUnit
        );

        $f = FileSize::from('4360000KiB');
        $f = $f->nearest(5, units: FileSize::UNITS_GROUP_BYTES_BINARY);

        assertEquals(
            '4.15802GiB',
            $f->format()
        );

        assertEquals(
            FileSize::UNIT_GIBIBYTES,
            $f->baseUnit
        );

        $f = FileSize::from('0.000001245TiB');
        $f = $f->nearest(5, units: FileSize::UNITS_GROUP_BYTES_BINARY);

        assertEquals(
            '1.30547MiB',
            $f->format()
        );

        assertEquals(
            FileSize::UNIT_MEBIBYTES,
            $f->baseUnit
        );
    }

    #[Test]
    // nearest
    public function nearestWithPresetUnits(): void
    {
        $f = new FileSize()
            ->withOnlyBytesBinary()
            ->withParse('0.000001245TiB');

        $f = $f->nearest(5);

        assertEquals(
            '1.30547MiB',
            $f->format()
        );

        assertEquals(
            FileSize::UNIT_MEBIBYTES,
            $f->baseUnit
        );
    }

    #[Test]
    public function serialize(): void
    {
        $d = Duration::parse('439567123458345956nanoseconds');
        $serialized = $d->serialize();

        assertEquals(
            '13years 11months 1week 19hours 34minutes 43seconds 458milliseconds 345microseconds 956nanoseconds',
            $serialized,
        );

        $new = Duration::parse($serialized);

        assertTrue(
            $new->value->isEqualTo($d->value)
        );

        $serialized = $d->serialize(
            [
                Duration::UNIT_NANOSECONDS,
                Duration::UNIT_MONTHS,
                Duration::UNIT_SECONDS,
            ]
        );

        assertEquals(
            '167months 450643seconds 458345956nanoseconds',
            $serialized,
        );

        $new = Duration::parse($serialized);

        assertTrue(
            $new->value->isEqualTo($d->value)
        );
    }
}
