<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests;

use Asika\BetterUnits\FileSize;
use Asika\BetterUnits\Duration;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileSizeTest extends TestCase
{
    #[DataProvider('bytesBinaryProvider')]
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

    public static function bytesBinaryProvider(): array
    {
        return [
            '1024KiB to MiB' => [
                fn () => static::createBytesBinary(1024, FileSize::UNIT_KIBIBYTES)
                    ->convertTo(FileSize::UNIT_MEBIBYTES),
                [],
                [],
                '1MiB',
                '1MiB',
            ],
            '124536KiB to MiB' => [
                fn () => static::createBytesBinary(124536, FileSize::UNIT_KIBIBYTES)
                    ->convertTo(FileSize::UNIT_MEBIBYTES, 3),
                [],
                [],
                '121.617MiB',
                '121MiB 631KiB 827B 3b',
            ],
            '1MiB to KiB' => [
                fn () => static::createBytesBinary(1, FileSize::UNIT_MEBIBYTES)
                    ->convertTo(FileSize::UNIT_KIBIBYTES),
                [],
                [],
                '1024KiB',
                '1MiB',
            ],
            '10GiB to MiB' => [
                fn () => static::createBytesBinary('10GiB')
                    ->convertTo('MiB'),
                [],
                [],
                '10240MiB',
                '10GiB',
            ],
            '1,525GiB to MiB' => [
                fn () => static::createBytesBinary('1,525GiB')
                    ->convertTo(FileSize::UNIT_MEBIBYTES),
                [],
                [],
                '1561600MiB',
                '1TiB 501GiB',
            ],
        ];
    }

    public static function createBytesBinary(mixed $value = 0, ?string $unit = null): FileSize
    {
        return new FileSize()
            ->withOnlyBytesBinary()
            ->withFrom($value, $unit);
    }

    public static function createBitsBinary(mixed $value = 0, ?string $unit = null): FileSize
    {
        return new FileSize()
            ->withOnlyBitsBinary()
            ->withFrom($value, $unit);
    }

    public static function createBytesBase10(mixed $value = 0, ?string $unit = null): FileSize
    {
        return new FileSize()
            ->withOnlyBytesBase10()
            ->withFrom($value, $unit);
    }

    public static function createBitsBase10(mixed $value = 0, ?string $unit = null): FileSize
    {
        return new FileSize()
            ->withOnlyBitsBase10()
            ->withFrom($value, $unit);
    }
}
