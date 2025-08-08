<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests;

use Asika\BetterUnits\Area;
use Asika\BetterUnits\Duration;
use Asika\BetterUnits\Length;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class AreaTest extends TestCase
{
    #[DataProvider('areaProvider')]
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
    }

    public static function areaProvider(): array
    {
        return [
            '1 square meter to square kilometers' => [
                fn () => new Area(1, Area::UNIT_SQUARE_METERS)
                    ->convertTo(Area::UNIT_SQUARE_KILOMETERS, scale: 8),
                [],
                [],
                '0.000001km2',
                '1m2',
            ],
            '1 square kilometer to square meters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_KILOMETERS)
                    ->convertTo(Area::UNIT_SQUARE_METERS, scale: 8),
                [],
                [],
                '1000000m2',
                '1km2',
            ],
            '1 square meter to square centimeters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_METERS)
                    ->convertTo(Area::UNIT_SQUARE_CENTIMETERS, scale: 8),
                [],
                [],
                '10000cm2',
                '1m2',
            ],
            '1 square centimeter to square meters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_CENTIMETERS)
                    ->convertTo(Area::UNIT_SQUARE_METERS, scale: 8),
                [],
                [],
                '0.0001m2',
                '1cm2',
            ],
            '1 square meter to square millimeters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_METERS)
                    ->convertTo(Area::UNIT_SQUARE_MILLIMETERS, scale: 8),
                [],
                [],
                '1000000mm2',
                '1m2',
            ],
            '1 square millimeter to square meters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_MILLIMETERS)
                    ->convertTo(Area::UNIT_SQUARE_METERS, scale: 8),
                [],
                [],
                '0.000001m2',
                '1mm2',
            ],
            '1 cm2 to square micrometers' => [
                fn () => new Area(1, Area::UNIT_SQUARE_CENTIMETERS)
                    ->convertTo(Area::UNIT_SQUARE_MICROMETERS, scale: 8),
                [],
                [],
                '100000000μm2',
                '1cm2',
            ],
            '1 square micrometer to square centimeters' => [
                fn () => new Area(1, Area::UNIT_SQUARE_MICROMETERS)
                    ->convertTo(Area::UNIT_SQUARE_CENTIMETERS, scale: 8),
                [],
                [],
                '0.00000001cm2',
                '1μm2',
            ],
        ];
    }

    #[Test]
    public function to()
    {
        $a = Area::from('100 square meters')->toSquareMillimeters();

        assertEquals(
            '100000000',
            (string) $a,
        );
    }
}
