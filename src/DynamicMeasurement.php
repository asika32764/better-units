<?php

declare(strict_types=1);

namespace Asika\UnitConverter;

class DynamicMeasurement extends AbstractMeasurement
{
    public string $atomUnit;

    public string $defaultUnit;

    protected array $unitExchanges = [];

    public function __construct(string $atomUnit, string $defaultUnit, array $unitExchanges)
    {
        $this->atomUnit = $atomUnit;
        $this->defaultUnit = $defaultUnit;
        $this->unitExchanges = $unitExchanges;

        parent::__construct(0, $defaultUnit);
    }
}
