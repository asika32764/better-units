<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\AbstractUnitConverter;
use Asika\UnitConverter\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    protected Duration $instance;

    #[DataProvider('secondsToOthersProvider')]
    #[DataProvider('hoursToOthersProvider')]
    #[DataProvider('calendarsProvider')]
    public function testConstructAndConvert(
        AbstractUnitConverter|\Closure $converter,
        array|\Closure $formatArgs,
        array|\Closure $humanizeArgs,
        string $formatted,
        string $humanized,
    ): void {
        if ($converter instanceof \Closure) {
            $converter = $converter();
        }

        self::assertEquals(
            $formatted,
            $formatArgs instanceof \Closure
                ? $formatArgs($converter)
                : $converter->format(...$formatArgs)
        );
        self::assertEquals(
            $humanized,
            $humanizeArgs instanceof \Closure
                ? $humanizeArgs($converter)
                : $converter->humanize(...$humanizeArgs),
        );

        // Test time correct
        $microSeconds = $converter->convertTo(Duration::UNIT_MICROSECONDS, 0);

        $interval = \DateInterval::createFromDateString($microSeconds->humanize());
        $tm = $microSeconds->intervalToMicroseconds($interval);

        self::assertEquals(
            (string) $tm->toBigDecimal()->stripTrailingZeros(),
            (string) $microSeconds->value->toBigDecimal()->stripTrailingZeros(),
        );
    }

    public static function secondsToOthersProvider(): array
    {
        return [
            '60 seconds to minute' => [
                fn () => new Duration(60)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '1minutes',
                '1minutes',
            ],
            '30 seconds to minutes no scale' => [
                fn () => new Duration(30)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '0minutes',
                '0minutes',
            ],
            '25 seconds to minutes no scale' => [
                fn () => new Duration(25)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '0minutes',
                '0minutes',
            ],
            '25 seconds to minutes scale 3' => [
                fn () => new Duration(25)->convertTo(Duration::UNIT_MINUTES, 3),
                fn (Duration $c) => $c->format(scale: 3),
                [],
                '0.416minutes',
                '24seconds 960milliseconds',
            ],
            '300 seconds to minutes' => [
                fn () => new Duration(300)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '5minutes',
                '5minutes',
            ],
            '375 seconds to minutes' => [
                fn () => new Duration(375)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '6minutes',
                '6minutes',
            ],
            '375 seconds to minutes scale 2' => [
                fn () => new Duration(375)->convertTo(Duration::UNIT_MINUTES, 2),
                fn (Duration $c) => $c->format(scale: 2),
                [],
                '6.25minutes',
                '6minutes 15seconds',
            ],
            '102500 seconds to hours no scale' => [
                fn () => new Duration(102500)->convertTo(Duration::UNIT_HOURS, 0),
                fn (Duration $c) => $c->format(),
                [],
                '28hours',
                '1days 4hours',
            ],
            '102500 seconds to hours scale 3' => [
                fn () => new Duration(102500)->convertTo(Duration::UNIT_HOURS, 3),
                fn (Duration $c) => $c->format(scale: 3),
                [],
                '28.472hours',
                '1days 4hours 28minutes 19seconds 200milliseconds',
            ],
        ];
    }

    public static function hoursToOthersProvider(): array
    {
        return [
            '1 hour to minute' => [
                fn () => new Duration(1, Duration::UNIT_HOURS)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '60minutes',
                '1hours',
            ],
            '3.74 hours to minutes' => [
                fn () => new Duration(3.74, Duration::UNIT_HOURS)->convertTo(Duration::UNIT_MINUTES),
                [],
                [],
                '224.4minutes',
                '3hours 44minutes 24seconds',
            ],
            '1024 hours to days' => [
                fn () => new Duration(1024, Duration::UNIT_HOURS)->convertTo(Duration::UNIT_DAYS),
                [],
                [],
                '42days',
                '1months 1weeks 4days 13hours 36minutes',
            ],
            '16384 hours to years scale 3' => [
                fn () => new Duration(16384, Duration::UNIT_HOURS)->convertTo(Duration::UNIT_YEARS, 3),
                [],
                [],
                '1.87years',
                '1years 10months 1weeks 6days 5hours 12minutes',
            ],
        ];
    }

    public static function calendarsProvider(): array
    {
        return [
            'Common' => [
                function () {
                    return Duration::parse('1year 1.5month');
                },
                [],
                [],
                '35480160seconds',
                '1years 1months 2weeks 1days 5hours 12minutes',
            ],
            'Anomalistic' => [
                fn() => new Duration()
                    ->withAnomalisticCalendar()
                    ->withParse('1year 1.5month'),
                [],
                [],
                '35129502.23seconds',
                '1years 1months 1weeks 6days 18hours 39minutes 16seconds 560milliseconds',
            ],
            'Gregorian' => [
                fn() => new Duration()
                    ->withGregorianCalendar()
                    ->withParse('1year 1.5month'),
                [],
                [],
                '35501571seconds',
                '1years 1months 2weeks 1days 5hours 14minutes 33seconds',
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->instance = new Duration();
    }
}
