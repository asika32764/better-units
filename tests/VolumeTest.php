<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\Area;
use Asika\UnitConverter\Duration;
use Asika\UnitConverter\Volume;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VolumeTest extends TestCase
{
    #[DataProvider('volumeProvider')]
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

    public static function volumeProvider(): array
    {
        return [
            '1 cubic meter to cubic kilometers' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_METERS)
                    ->convertTo(Volume::UNIT_CUBIC_KILOMETERS, scale: 12),
                [],
                [],
                '0.000000001km3',
                '1m3',
            ],
            '1 cubic kilometer to cubic meters' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_KILOMETERS)
                    ->convertTo(Volume::UNIT_CUBIC_METERS, scale: 12),
                [],
                [],
                '1000000000m3',
                '1km3',
            ],
            '1 cubic meter to cubic centimeters' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_METERS)
                    ->convertTo(Volume::UNIT_CUBIC_CENTIMETERS, scale: 12),
                [],
                [],
                '1000000cm3',
                '1m3',
            ],
            '1 cubic centimeter to cubic meters' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_CENTIMETERS)
                    ->convertTo(Volume::UNIT_CUBIC_METERS, scale: 12),
                [],
                [],
                '0.000001m3',
                '1cm3',
            ],
            '1 cubic meter to liters' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_METERS)
                    ->convertTo(Volume::UNIT_CUBIC_LITERS, scale: 12),
                [],
                [],
                '1000L',
                '1m3',
            ],
            '1 liter to cubic meters' => [
                fn () => new Volume(1, Volume::UNIT_CUBIC_LITERS)
                    ->convertTo(Volume::UNIT_CUBIC_METERS, scale: 12),
                [],
                [],
                '0.001m3',
                '1dm3',
            ],
        ];
    }
}
