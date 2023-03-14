<?php

namespace DoseCalculator\Tests;

use DoseCalculator\Contracts\MedicineContract;

/**
 * Sample class implementing MedicineContract
 *
 * @internal
 */
class Medicine implements MedicineContract
{
    public function __construct(
        public string $name,
        protected ?string $min_dose = null,
        protected ?string $max_dose = null,
        protected ?string $concentration = null
    ) {
    }

    public function minDose(): ?string
    {
        return $this->min_dose;
    }

    public function maxDose(): ?string
    {
        return $this->max_dose;
    }

    public function concentration(): ?string
    {
        return $this->concentration;
    }
}
