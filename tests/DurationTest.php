<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\AbstractMeasurement;
use Asika\UnitConverter\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DurationTest extends TestCase
{
    #[DataProvider('secondsToOthersProvider')]
    #[DataProvider('hoursToOthersProvider')]
    #[DataProvider('calendarsProvider')]
    public function testConstructAndConvert(
        Duration|\Closure $converter,
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
                '1minute',
                '1minute',
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
                '1day 4hours',
            ],
            '102500 seconds to hours scale 3' => [
                fn () => new Duration(102500)->convertTo(Duration::UNIT_HOURS, 3),
                fn (Duration $c) => $c->format(scale: 3),
                [],
                '28.472hours',
                '1day 4hours 28minutes 19seconds 200milliseconds',
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
                '1hour',
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
                '1month 1week 4days 13hours 36minutes',
            ],
            '16384 hours to years scale 3' => [
                fn () => new Duration(16384, Duration::UNIT_HOURS)->convertTo(Duration::UNIT_YEARS, 3),
                [],
                [],
                '1.87years',
                '1year 10months 1week 6days 5hours 12minutes',
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
                '1year 1month 2weeks 1day 5hours 12minutes',
            ],
            'Anomalistic 1year' => [
                fn() => new Duration()
                    ->withAnomalisticCalendar()
                    ->withParse('1year'),
                [],
                [],
                '31558432.55seconds',
                '1year',
            ],
            'Anomalistic 1month' => [
                fn() => new Duration()
                    ->withAnomalisticCalendar()
                    ->withParse('1month'),
                [],
                [],
                '2380713.12seconds',
                '1month',
            ],
            'Anomalistic more' => [
                fn() => new Duration()
                    ->withAnomalisticCalendar()
                    ->withParse('1year 1.5month'),
                [],
                [],
                (Duration::YEAR_SECONDS_ANOMALISTIC + (1.5 * Duration::MONTH_SECONDS_ANOMALISTIC)) . 'seconds',
                '1year 1month 1week 6days 18hours 39minutes 16seconds 560milliseconds',
            ],
            'Gregorian' => [
                fn() => new Duration()
                    ->withGregorianCalendar()
                    ->withParse('1year 1.5month'),
                [],
                [],
                '35501571seconds',
                '1year 1month 2weeks 1day 5hours 14minutes 33seconds',
            ],
        ];
    }

    #[Test]
    public function dateInterval(): void
    {
        $d = Duration::parseDateString('3years 2months 17days 10hours 3minutes 45seconds 123milliseconds 456 microseconds');

        self::assertEquals(
            '3years 2months 2weeks 3days 10hours 3minutes 45seconds 123milliseconds 456microseconds',
            $d->humanize()
        );

        self::assertEquals(
            '101371905123456microseconds',
            $d->format(unit: Duration::UNIT_MICROSECONDS)
        );

        $interval = $d->toDateInterval();

        self::assertEquals(
            '3y 2m 2w 3d 10h 3m 45s.123456',
            sprintf(
                '%dy %dm %dw %dd %dh %dm %ds.%d',
                $interval->y,
                $interval->m,
                $interval->d / 7, // Convert days to weeks
                $interval->d % 7, // Remaining days
                $interval->h,
                $interval->i,
                $interval->s,
                (int) ($interval->f * 1_000_000) // Convert fractional seconds to microseconds
            )
        );
    }

    #[Test]
    public function toDateTime(): void
    {
        $now = '2025-03-02 00:00:00';

        $d = Duration::parse('3days');

        $future = $d->toFutureDateTime($now);

        self::assertEquals(
            '2025-03-05T00:00:00+00:00',
            $future->format(\DateTime::ATOM)
        );

        $future = $d->toPastDateTime($now);

        self::assertEquals(
            '2025-02-27T00:00:00+00:00',
            $future->format(\DateTime::ATOM)
        );
    }
}
