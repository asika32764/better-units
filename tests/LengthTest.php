<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\Duration;
use Asika\UnitConverter\FileSize;
use Asika\UnitConverter\Length;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LengthTest extends TestCase
{
    #[DataProvider('lengthProvider')]
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

    public static function lengthProvider(): array
    {
        return [
            '1,024m' => [
                fn () => Length::from('1,024m', Length::UNIT_METERS)
                    ->convertTo(Length::UNIT_KILOMETERS, 4),
                [],
                [],
                '1.024km',
                '1km 13fth 2h 2cm 2mm 400μm',
            ],
            '1km' => [
                fn () => Length::from('1km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_METERS, 2),
                [],
                [],
                '1000m',
                '1km',
            ],
            '1.5km' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->withOnlyCommonLengths()
                    ->convertTo(Length::UNIT_METERS, 2),
                [],
                [],
                '1500m',
                '1km 500m',
            ],
            '1.5km to miles' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_MILES, 4),
                [],
                [],
                '0.932mi',
                '1km 273fth 2ft 1in 1cm 1mm 208μm',
            ],
            '1.5km to yards' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_YARDS, 4),
                [],
                [],
                '1640.4199yd',
                '1km 273fth 2ft 1h 1in 956μm 560nm',
            ],
            '1.5km to inches' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_INCHES, 4),
                [],
                [],
                '59055.1181in',
                '1km 273fth 2ft 1h 1in 999μm 740nm',
            ],
            '1.5km to feet' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_FEET, 4),
                [],
                [],
                '4921.2598ft',
                '1km 273fth 2ft 1h 1in 987μm 40nm',
            ],
            '1.5km to micrometers' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_MICROMETERS, 4),
                [],
                [],
                '1500000000μm',
                '1km 273fth 2ft 1h 1in 1mm',
            ],
            '1.5km to nanometers' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_NANOMETERS, 4),
                [],
                [],
                '1500000000000nm',
                '1km 273fth 2ft 1h 1in 1mm',
            ],
            '1.5km to picometers' => [
                fn () => Length::from('1.5km', Length::UNIT_KILOMETERS)
                    ->convertTo(Length::UNIT_PICOMETERS, 4),
                [],
                [],
                '1500000000000000pm',
                '1km 273fth 2ft 1h 1in 1mm',
            ],
            '1.5light year to parsec' => [
                fn () => Length::from('1.5ly', Length::UNIT_LIGHT_YEARS)
                    ->convertTo(Length::UNIT_PARSEC, 4),
                [],
                [],
                '0.4599pc',
                '1ly 31618au 24687627nmi 1km 79fth 1h 2cm 3mm 200μm',
            ],
        ];
    }
}
