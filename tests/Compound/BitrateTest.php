<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests\Compound;

use Asika\BetterUnits\Compound\Bitrate;
use Asika\BetterUnits\Compound\Speed;
use Asika\BetterUnits\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BitrateTest extends TestCase
{
    #[DataProvider('bitrateProvider')]
    #[DataProvider('presetProvider')]
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

    public static function bitrateProvider(): array
    {
        return [
            'MiB/s to bit/s' => [
                fn () => new Bitrate(1, 'MiB/s')
                    ->convertTo('bit/s', scale: 10),
                [],
                '8388608b/s',
            ],
            'MiB/s to Kib/s' => [
                fn () => new Bitrate(1, 'MiB/s')
                    ->convertTo('Kib/s', scale: 10),
                [],
                '8192Kib/s',
            ],
            'MiB/s to KiB/s' => [
                fn () => new Bitrate(1, 'MiB/s')
                    ->convertTo('KiB/s', scale: 10),
                [],
                '1024KiB/s',
            ],
            'MiB/s to B/s' => [
                fn () => new Bitrate(1, 'MiB/s')
                    ->convertTo('B/s', scale: 10),
                [],
                '1048576B/s',
            ],
            'KiB/s to GiB/s' => [
                fn () => Bitrate::from('1KiB/s')
                    ->convertTo('GiB/s', scale: 15),
                [],
                '0.000000953674316GiB/s',
            ],
            'KiB/s to GB/s' => [
                fn () => Bitrate::from('1KiB/s')
                    ->convertTo('GB/s', scale: 15),
                [],
                '0.000001024GB/s',
            ],
            'KiB/s to Gib/s' => [
                fn () => Bitrate::from('1KiB/s')
                    ->convertTo('Gib/s', scale: 15),
                [],
                '0.000007629394531Gib/s',
            ],
            'KiB/s to Gb/s' => [
                fn () => Bitrate::from('1KiB/s')
                    ->convertTo('Gb/s', scale: 15),
                [],
                '0.000008192Gb/s',
            ],
        ];
    }

    public static function presetProvider(): array
    {
        return [
            'Mbps to Mibps' => [
                fn () => Bitrate::parse('1 Mbps')
                    ->convertTo('Mibps', scale: 10),
                [],
                '0.9536743164Mibps',
            ],
            'Mbps to Kbps' => [
                fn () => Bitrate::parse('1 Mbps')
                    ->convertTo('Kbps', scale: 10),
                [],
                '1000Kbps',
            ],
            'Mbps to Kibps' => [
                fn () => Bitrate::parse('1 Mbps')
                    ->convertTo('Kibps', scale: 10),
                [],
                '976.5625Kibps',
            ],
            'Gibps to Mbps' => [
                fn () => Bitrate::parse('1 Gbps')
                    ->convertTo('Mbps', scale: 10),
                [],
                '1000Mbps',
            ],
            'Gibps to Mibps' => [
                fn () => Bitrate::parse('1 Gbps')
                    ->convertTo('Mibps', scale: 10),
                [],
                '953.6743164062Mibps',
            ],
        ];
    }
}
