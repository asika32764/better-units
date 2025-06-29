<?php

declare(strict_types=1);

namespace Asika\UnitConverter\Concerns;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;

trait CalculationTrait
{
    public function plus(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY,
    ): static {
        $new = clone $this;
        $new->value = $new->value->plus($this->preprocessThat($that, $scale, $roundingMode));

        return $new;
    }

    public function minus(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY,
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
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY
    ): static {
        $new = clone $this;
        $new->value = $new->value->dividedBy($that, $scale, $roundingMode);

        return $new;
    }

    private function preprocessThat(
        BigNumber|int|float|string|self $that,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY,
    ): BigDecimal {
        if ($that instanceof self) {
            return $that->convertTo($this->unit, $scale, $roundingMode)->value;
        }

        return BigDecimal::of($that);
    }
}
