<?php

namespace DoseCalculator;

use DoseCalculator\Contracts\HasWeightsContract;
use DoseCalculator\Exceptions\DoseCalculationException;
use DoseCalculator\Contracts\MedicineContract;

/**
 * Class DoseCalculator
 *
 * @package DoseCalculator
 */
class DoseCalculator
{
    /**
     * @param MedicineContract $medicine
     */
    public function __construct(
        public MedicineContract $medicine,
    ) {
    }

    /**
     * @param string|null $value
     * @param string      $numerator
     * @param string      $denominator
     * @param int         $precision
     *
     * @return string|null
     */
    private function parseFraction(?string $value, string $numerator, string $denominator, int $precision): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = preg_replace("/\s+/", "", $value);

        $parts = [];
        preg_match("/([0-9.]+){$numerator}\/([0-9.]*){$denominator}/", $value, $parts);

        if (count($parts) !== 3) {
            throw new DoseCalculationException("Value must be in {$numerator}/{$denominator}");
        }

        $mg = $parts[1];
        $kg = $parts[2] ?: 1;

        if ((int)$kg === 0) {
            $kg = 1;
        }

        return bcdiv($mg, $kg, $precision);
    }

    /**
     * @param string|null $dose
     * @param int         $precision
     *
     * @return string|null
     */
    public function dosePerKg(?string $dose, int $precision = 2): ?string
    {
        return $this->parseFraction($dose, 'mg', 'kg', $precision);
    }

    /**
     * @param int $precision
     *
     * @return string|null
     */
    public function minDosePerKg(int $precision = 2): ?string
    {
        return $this->dosePerKg($this->medicine->minDose(), $precision);
    }

    /**
     * @param int $precision
     *
     * @return string|null
     */
    public function maxDosePerKg(int $precision = 2): ?string
    {
        return $this->dosePerKg($this->medicine->maxDose() ?? $this->medicine->minDose(), $precision);
    }

    /**
     * @param int $precision
     *
     * @return string|null
     */
    public function concentrationPerMl(int $precision = 2): ?string
    {
        return $this->parseFraction($this->medicine->concentration(), 'mg', 'mL', $precision);
    }

    /**
     * @param HasWeightsContract $model
     * @param int                $final_precision
     *
     * @return DoseResult
     */
    public function calculateFor(HasWeightsContract $model, int $final_precision = 2): DoseResult
    {
        $weight = $model->weights()
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $weight) {
            throw new DoseCalculationException("No weights logged");
        }

        // TODO: add contract for Weight class
        /** @phpstan-ignore-next-line */
        return $this->calculateRange($weight->weight, $final_precision);
    }

    /**
     * @param float $weight_in_kg
     * @param bool  $max
     * @param int   $final_precision
     *
     * @return DoseResult
     */
    public function calculate(float $weight_in_kg, bool $max = false, int $final_precision = 2): DoseResult
    {
        $concentration_in_mg_per_ml = $this->concentrationPerMl($final_precision + 1);
        $dosing_in_mg_per_kg = $max
            ? $this->maxDosePerKg($final_precision + 1)
            : $this->minDosePerKg($final_precision + 1);

        if ($concentration_in_mg_per_ml === null) {
            throw new DoseCalculationException("Unable to calculate dose - missing concentration");
        }
        if ($dosing_in_mg_per_kg === null) {
            throw new DoseCalculationException("Unable to calculate dose - missing dosing information");
        }

        /***********************************************
         *
         *        dosing * weight   (mg/kg) * (kg)
         * Dose = --------------- = -------------- = mL
         *         concentration       (mg/mL)
         *
         ***********************************************/

        $dose = bcdiv(
            bcmul($dosing_in_mg_per_kg, (string)$weight_in_kg, $final_precision + 1),
            $concentration_in_mg_per_ml,
            $final_precision + 1
        );

        // Want to make sure we round up if > .5
        return new DoseResult(bcround($dose, $final_precision), unit: 'mL');
    }

    /**
     * @param float $weight_in_kg
     * @param int   $final_precision
     *
     * @return DoseResult
     */
    public function calculateRange(float $weight_in_kg, int $final_precision = 2): DoseResult
    {
        $min = $this->calculate($weight_in_kg, final_precision: $final_precision);
        $max = $this->calculate($weight_in_kg, max: true, final_precision: $final_precision);

        if ($min->equals($max)) {
            return $min;
        }

        return new DoseResult($min->min, $max->min, 'mL');
    }
}
