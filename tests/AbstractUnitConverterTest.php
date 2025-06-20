<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Tests;

use Asika\UnitConverter\AbstractUnitConverter;
use Asika\UnitConverter\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AbstractUnitConverterTest extends TestCase
{
    protected Duration $instance;

    #[DataProvider('constructorAndConvertProvider')]
    public function testConstructAndConvert(
        AbstractUnitConverter|\Closure $converter,
        array|\Closure $formatArgs,
        array|\Closure $humanizeArgs,
        string $formatted,
        string $humanized
    ) {
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

    public static function constructorAndConvertProvider()
    {
        return [
            '60 seconds' => [
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
        ];
    }

    protected function setUp(): void
    {
        $this->instance = new Duration();
    }
}
