<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Tests;

use Asika\BetterUnits\Duration;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{
    public function testPlus(): void
    {
        $d = Duration::from(100, Duration::UNIT_MINUTES)
            ->plus(50);

        self::assertEquals('150minutes', $d->format());

        $d = Duration::from(100, Duration::UNIT_MINUTES)
            ->plus(BigDecimal::of('5.3'))
            ->convertTo(Duration::UNIT_SECONDS);

        self::assertEquals('6318seconds', $d->format());

        $d = Duration::from(100, Duration::UNIT_MINUTES)
            ->plus(Duration::from('1hour'));

        self::assertEquals('160minutes', $d->format());

        $d = Duration::from(100, Duration::UNIT_MINUTES)
            ->plus(Duration::from('256seconds'), 2, RoundingMode::HalfUp);

        self::assertEquals('104.27minutes', $d->format());
    }

    public function testCompare(): void
    {
        $d = Duration::from(100, Duration::UNIT_MINUTES);

        self::assertTrue($d->isGreaterThan(50));

        self::assertTrue($d->isGreaterThan(Duration::from(1, Duration::UNIT_HOURS)));

        self::assertTrue($d->isGreaterThan(1, Duration::UNIT_HOURS));

        self::assertTrue($d->isLessThan(Duration::from(2, Duration::UNIT_HOURS)));

        self::assertTrue($d->isEquals(Duration::from(6000, Duration::UNIT_SECONDS)));

        self::assertTrue(
            $d->isEquals(Duration::from(6005, Duration::UNIT_SECONDS), 0, RoundingMode::Down)
        );

        self::assertFalse($d->isEquals(6000));
    }
}
