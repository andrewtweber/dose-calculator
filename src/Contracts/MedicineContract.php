<?php

namespace DoseCalculator\Contracts;

/**
 * Interface MedicineContract
 *
 * @package DoseCalculator\Contracts
 */
interface MedicineContract
{
    /**
     * Sample formats. All whitespace is ignored:
     *   "20mg/kg"
     *   "15mg/3kg"
     *
     * The method return type is nullable, but the DoseCalculator will fail if this is not set
     *
     * @return string|null
     */
    public function minDose(): ?string;

    /**
     * Same format as minDose. The maxDose is optional
     *
     * @return string|null
     */
    public function maxDose(): ?string;

    /**
     * Sample formats. All whitespace is ignored:
     *   "200mg/mL"
     *   "100mg/5mL"
     *
     * The method return type is nullable, but the DoseCalculator will fail if this is not set
     *
     * @return string|null
     */
    public function concentration(): ?string;
}
