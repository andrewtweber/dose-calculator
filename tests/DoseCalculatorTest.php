<?php

namespace DoseCalculator\Tests;

use DoseCalculator\DoseCalculator;
use DoseCalculator\Exceptions\DoseCalculationException;

/**
 * Class DoseCalculatorTest
 *
 * @package Tests\Feature\Services
 */
class DoseCalculatorTest extends TestCase
{
    /**
     * @test
     */
    public function dosing_calculation(): void
    {
        $medicine = new Medicine(
            name: 'Medicine 1',
            min_dose: '20mg/kg'
        );

        // Min and max dose are the same
        $calc = new DoseCalculator($medicine);
        $this->assertSame('20.00', $calc->minDosePerKg());
        $this->assertSame('20.00', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 2',
            min_dose: '10mg/kg',
            max_dose: '40mg/kg',
        );

        // Max dose is different
        $calc = new DoseCalculator($medicine);
        $this->assertSame('10.00', $calc->minDosePerKg());
        $this->assertSame('40.00', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 3',
            min_dose: '20 mg / kg',
            max_dose: ' 30 mg / kg ',
        );

        // Whitespace is ignored
        $calc = new DoseCalculator($medicine);
        $this->assertSame('20.00', $calc->minDosePerKg());
        $this->assertSame('30.00', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 4',
            min_dose: '15mg / 1kg',
            max_dose: '45mg / 1kg ',
        );

        // Has kg specified
        $calc = new DoseCalculator($medicine);
        $this->assertSame('15.00', $calc->minDosePerKg());
        $this->assertSame('45.00', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 5',
            min_dose: '15mg / 3kg',
            max_dose: '45mg / 4kg',
        );

        // Has kg specified with value > 1
        $calc = new DoseCalculator($medicine);
        $this->assertSame('5.00', $calc->minDosePerKg());
        $this->assertSame('11.25', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 6',
            min_dose: '15mg / 0kg',
            max_dose: '45mg / 0kg ',
        );

        // 0 kg is corrected to 1 kg
        $calc = new DoseCalculator($medicine);
        $this->assertSame('15.00', $calc->minDosePerKg());
        $this->assertSame('45.00', $calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 7',
            min_dose: null,
            max_dose: null,
        );

        // Null doses
        $calc = new DoseCalculator($medicine);
        $this->assertNull($calc->minDosePerKg());
        $this->assertNull($calc->maxDosePerKg());

        $medicine = new Medicine(
            name: 'Medicine 8',
            min_dose: '3mg',
            max_dose: null,
        );

        $this->expectException(DoseCalculationException::class);
        $this->expectExceptionMessage("Value must be in mg/kg");

        // Invalid format partial match
        $calc = new DoseCalculator($medicine);
        $calc->minDosePerKg();
    }

    /**
     * @test
     */
    public function concentration_calculation(): void
    {
        $medicine = new Medicine(
            name: 'Medicine 1',
            concentration: '200mg/mL',
        );

        // Standard
        $calc = new DoseCalculator($medicine);
        $this->assertSame('200.00', $calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 2',
            concentration: ' 100 mg / mL ',
        );

        // Whitespace is ignored
        $calc = new DoseCalculator($medicine);
        $this->assertSame('100.00', $calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 3',
            concentration: '200mg / 1mL',
        );

        // Has mL specified
        $calc = new DoseCalculator($medicine);
        $this->assertSame('200.00', $calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 4',
            concentration: '200mg / 6mL',
        );

        // Has mL specified with value > 1
        $calc = new DoseCalculator($medicine);
        $this->assertSame('33.33', $calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 5',
            concentration: '100mg / 0mL',
        );

        // 0 mL is corrected to 1 mL
        $calc = new DoseCalculator($medicine);
        $this->assertSame('100.00', $calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 6',
            concentration: null,
        );

        // Null concentration
        $calc = new DoseCalculator($medicine);
        $this->assertNull($calc->concentrationPerMl());

        $medicine = new Medicine(
            name: 'Medicine 7',
            concentration: '3mg',
        );

        $this->expectException(DoseCalculationException::class);
        $this->expectExceptionMessage("Value must be in mg/mL");

        // Invalid format partial match
        $calc = new DoseCalculator($medicine);
        $calc->concentrationPerMl();
    }

    /**
     * @test
     */
    public function dose_calculation_fails_with_concentration_missing(): void
    {
        $medicine = new Medicine(
            name: 'Medicine 1',
            min_dose: '20mg/kg',
            concentration: null,
        );

        $this->expectException(DoseCalculationException::class);
        $this->expectExceptionMessage("Unable to calculate dose - missing concentration");

        $calc = new DoseCalculator($medicine);
        $this->assertNull($calc->calculate(0.834));
    }

    /**
     * @test
     */
    public function dose_calculation_fails_with_min_dose_missing(): void
    {
        $medicine = new Medicine(
            name: 'Medicine 1',
            min_dose: null,
            concentration: '50mg/mL',
        );

        $this->expectException(DoseCalculationException::class);
        $this->expectExceptionMessage("Unable to calculate dose - missing dosing information");

        $calc = new DoseCalculator($medicine);
        $this->assertNull($calc->calculate(0.834));
    }

    /**
     * @test
     */
    public function dose_calculation_for_amoxicillin(): void
    {
        $medicine = new Medicine(
            name: 'Amoxicillin',
            min_dose: '20mg/kg',
            concentration: '50mg/mL',
        );

        $calc = new DoseCalculator($medicine);
        $this->assertSame('0.33', $calc->calculate(0.834));
        $this->assertSame('0.33', $calc->calculate(0.834, max: true));
        $this->assertSame('0.33', $calc->calculateRange(0.834));
        $this->assertSame('0.334', $calc->calculate(0.834, final_precision: 3));
        $this->assertSame('0.3336', $calc->calculate(0.834, final_precision: 4));
    }

    /**
     * @test
     */
    public function dose_calculation_for_medication_with_range_and_rounding_up(): void
    {
        $medicine = new Medicine(
            name: 'Amoxicillin',
            min_dose: '20mg/kg',
            max_dose: '40mg/kg',
            concentration: '50mg/mL',
        );

        $calc = new DoseCalculator($medicine);
        $this->assertSame('0.33', $calc->calculate(0.834));
        $this->assertSame('0.67', $calc->calculate(0.834, max: true));
        $this->assertSame('0.33 - 0.67', $calc->calculateRange(0.834));
        $this->assertSame('0.334 - 0.667', $calc->calculateRange(0.834, final_precision: 3));
        $this->assertSame('0.3336 - 0.6672', $calc->calculateRange(0.834, final_precision: 4));
    }

    /**
     * @test
     */
    public function dose_calculation_for_azithromycin(): void
    {
        $medicine = new Medicine(
            name: 'Azithromycin',
            min_dose: '5mg/kg',
            concentration: '100 mg/ 5mL',
        );

        $calc = new DoseCalculator($medicine);
        $this->assertSame('0.30', $calc->calculate(1.2));
        $this->assertSame('0.3', $calc->calculate(1.2, final_precision: 1));
    }

    /**
     * @test
     */
    public function dose_calculation_for_famotidine(): void
    {
        $medicine = new Medicine(
            name: 'Famotidine',
            min_dose: '0.5mg/kg',
            concentration: '4 mg/mL',
        );

        $calc = new DoseCalculator($medicine);
        $this->assertSame('0.16', $calc->calculate(1.31));
        $this->assertSame('0.2', $calc->calculate(1.31, final_precision: 1));
    }
}
