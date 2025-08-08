<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests;

use Asika\BetterUnits\Duration;
use Asika\BetterUnits\Volume;
use Asika\BetterUnits\Weight;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WeightTest extends TestCase
{
    #[DataProvider('weightProvider')]
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

    public static function weightProvider(): array
    {
        return [
            '1 gram to kilograms' => [
                fn () => new Weight(1, Weight::UNIT_GRAMS)
                    ->convertTo(Weight::UNIT_KILOGRAMS, scale: 4),
                [],
                [],
                '0.001kg',
                '1g',
            ],
            '1 kilogram to grams' => [
                fn () => new Weight(1, Weight::UNIT_KILOGRAMS)
                    ->convertTo(Weight::UNIT_GRAMS, scale: 4),
                [],
                [],
                '1000g',
                '1kg',
            ],
            '1 gram to milligrams' => [
                fn () => new Weight(1, Weight::UNIT_GRAMS)
                    ->convertTo(Weight::UNIT_MILLIGRAMS, scale: 4),
                [],
                [],
                '1000mg',
                '1g',
            ],
            '1 milligram to grams' => [
                fn () => new Weight(1, Weight::UNIT_MILLIGRAMS)
                    ->convertTo(Weight::UNIT_GRAMS, scale: 4),
                [],
                [],
                '0.001g',
                '1mg',
            ],
            '1 kilogram to milligrams' => [
                fn () => new Weight(1, Weight::UNIT_KILOGRAMS)
                    ->convertTo(Weight::UNIT_MILLIGRAMS, scale: 4),
                [],
                [],
                '1000000mg',
                '1kg',
            ],
            '1 milligram to kilograms' => [
                fn () => new Weight(1, Weight::UNIT_MILLIGRAMS)
                    ->convertTo(Weight::UNIT_KILOGRAMS, scale: 8),
                [],
                [],
                '0.000001kg',
                '1mg',
            ],
            '2 ounce to grams' => [
                fn () => new Weight(2, Weight::UNIT_OUNCES)
                    ->convertTo(Weight::UNIT_GRAMS, scale: 4),
                [],
                [],
                '56.699g',
                '1oz 28g 1ct 1N 4cg 7mg 476μg 875ng',
            ],
            '1 ounce to pounds' => [
                fn () => new Weight(1, Weight::UNIT_OUNCES)
                    ->convertTo(Weight::UNIT_POUNDS, scale: 4),
                [],
                [],
                '0.0625lb',
                '1oz',
            ],
            '1 pound to ounces' => [
                fn () => new Weight(1, Weight::UNIT_POUNDS)
                    ->convertTo(Weight::UNIT_OUNCES, scale: 4),
                [],
                [],
                '16oz',
                '1lb',
            ],
            '2 pound to grams' => [
                fn () => new Weight(2, Weight::UNIT_POUNDS)
                    ->convertTo(Weight::UNIT_GRAMS, scale: 4),
                [],
                [],
                '907.1847g',
                '1lb 15oz 28g 1ct 1N 4cg 7mg 483μg 125ng',
            ],
        ];
    }
}
