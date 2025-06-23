<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests\Compound;

use Asika\UnitConverter\Area;
use Asika\UnitConverter\Compound\Speed;
use Asika\UnitConverter\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class SpeedTest extends TestCase
{
    #[DataProvider('speedProvider')]
    public function testConstructAndConvert(
        Duration|\Closure $converter,
        array|\Closure $formatArgs,
        string $formatted,
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
    }

    public static function speedProvider(): array
    {
        return [
            'km/h to m/s' => [
                fn () => new Speed(1, 'km/h')
                    ->convertTo('m/s', scale: 10),
                [],
                '0.2777777777m/s',
            ],
            'm/s to km/h' => [
                fn () => new Speed(1, 'm/s')
                    ->convertTo('km/h', scale: 10),
                [],
                '3.6km/h',
            ],
            'm/s to km/h with format' => [
                fn () => new Speed(1, 'm/s')
                    ->convertTo('km/h', scale: 10),
                fn (Speed $speed) => $speed->format('KMH'),
                '3.6KMH',
            ],
            'mps to kph' => [
                fn () => Speed::parse('1mps')
                    ->convertTo('kph', scale: 10),
                [],
                '3.6km/h',
            ],
            'kph to mps' => [
                fn () => Speed::parse('1kph')
                    ->convertTo('mps', scale: 10),
                [],
                '0.2777777777m/s',
            ],
            'mph to mps' => [
                fn () => Speed::parse('1mph')
                    ->convertTo('mps', scale: 10),
                [],
                '0.44704m/s',
            ],
        ];
    }
}
