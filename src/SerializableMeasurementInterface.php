<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

use Brick\Math\RoundingMode;

interface SerializableMeasurementInterface extends MeasurementInterface
{
    public function withParse(
        string $value,
        ?string $asUnit = null,
        ?int $scale = null,
        RoundingMode $roundingMode = RoundingMode::DOWN
    ): static;

    /**
     * @param  string  $unit
     *
     * @return  array{ static, static }
     */
    public function withExtract(string $unit): array;

    public function serialize(?array $units = null): string;

    public function serializeCallback(\Closure $callback): string;
}
