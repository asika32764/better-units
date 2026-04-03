<?php

declare(strict_types=1);

namespace Asika\BetterUnits\Concerns;

use Asika\BetterUnits\ConvertHelper;
use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;

trait CalculationTrait
{
    public function plus(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): static {
        $new = clone $this;
        $new->value = $new->value->plus($this->preprocessThat($that, $scale, $roundingMode));

        return $new;
    }

    public function minus(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): static {
        $new = clone $this;
        $new->value = $new->value->minus($this->preprocessThat($that, $scale, $roundingMode));

        return $new;
    }

    public function multipliedBy(
        BigNumber|int|float|string $that,
    ): static {
        $new = clone $this;
        $new->value = $new->value->multipliedBy($that);

        return $new;
    }

    public function dividedBy(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary
    ): static {
        $new = clone $this;
        $new->value = $new->value->dividedBy($that, $scale, $roundingMode);

        return $new;
    }

    public function isEquals(
        BigNumber|int|float|string|self $that,
        int|string|null $scaleOrUnit = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): bool {
        return $this->value->isEqualTo($this->preprocessThat($that, $scaleOrUnit, $roundingMode));
    }

    public function isGreaterThan(
        BigNumber|int|float|string|self $that,
        int|string|null $scaleOrUnit = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): bool {
        return $this->value->isGreaterThan($this->preprocessThat($that, $scaleOrUnit, $roundingMode));
    }

    public function isGreaterThanOrEqualTo(
        BigNumber|int|float|string|self $that,
        int|string|null $scaleOrUnit = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): bool {
        return $this->value->isGreaterThanOrEqualTo($this->preprocessThat($that, $scaleOrUnit, $roundingMode));
    }

    public function isLessThan(
        BigNumber|int|float|string|self $that,
        int|string|null $scaleOrUnit = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): bool {
        return $this->value->isLessThan($this->preprocessThat($that, $scaleOrUnit, $roundingMode));
    }

    public function isLessThanOrEqualTo(
        BigNumber|int|float|string|self $that,
        int|string|null $scaleOrUnit = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): bool {
        return $this->value->isLessThanOrEqualTo($this->preprocessThat($that, $scaleOrUnit, $roundingMode));
    }

    private function preprocessThat(
        BigNumber|int|float|string|self $that,
        int|string|null $scale = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary,
    ): BigDecimal {
        if (!($that instanceof self) && is_string($scale)) {
            $that = static::from($that, $scale);

            $scale = null;
        }

        if ($that instanceof self) {
            return $that->convertTo($this->unit, $scale, $roundingMode)->value;
        }

        return ConvertHelper::toBigDecimal($that);
    }
}
