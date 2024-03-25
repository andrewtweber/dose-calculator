<?php

namespace DoseCalculator\Tests;

use DoseCalculator\DoseResult;

class DoseResultTest extends TestCase
{
    /**
     * @test
     */
    public function dose_result_formatting(): void
    {
        $result = new DoseResult('0.12');
        $this->assertSame('0.12 mL', (string)$result);

        $result = new DoseResult('0.12', unit: 'L');
        $this->assertSame('0.12 L', (string)$result);

        $result = new DoseResult('0.12', unit: null);
        $this->assertSame('0.12', (string)$result);

        $result = new DoseResult('0.12', '0.18');
        $this->assertSame('0.12 - 0.18 mL', (string)$result);

        $result = new DoseResult('0.12', '0.18', unit: 'L');
        $this->assertSame('0.12 - 0.18 L', (string)$result);

        $result = new DoseResult('0.12', '0.18', unit: null);
        $this->assertSame('0.12 - 0.18', (string)$result);
    }

    /**
     * @test
     */
    public function dose_result_when_min_and_max_are_same(): void
    {
        $result = new DoseResult('0.12', '0.12');
        $this->assertSame('0.12', $result->min);
        $this->assertNull($result->max);
        $this->assertSame('0.12 mL', (string)$result);

        $result = new DoseResult('0.12', '0.12', unit: 'L');
        $this->assertSame('0.12 L', (string)$result);

        $result = new DoseResult('0.12', '0.12', unit: null);
        $this->assertSame('0.12', (string)$result);
    }

    /**
     * @test
     */
    public function dose_result_comparison(): void
    {
        $result1 = new DoseResult('0.12');
        $result2 = new DoseResult('0.12', '0.12');
        $result3 = new DoseResult('0.12', '0.13');
        $result4 = new DoseResult('0.12', unit: 'L');

        $this->assertTrue($result1->equals($result2));
        $this->assertFalse($result1->equals($result3));
        $this->assertFalse($result1->equals($result4));
    }
}
