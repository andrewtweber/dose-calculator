<?php

namespace DoseCalculator;

class DoseResult
{
    public readonly ?string $max;

    public function __construct(
        public readonly string $min,
        ?string $max = null,
        public readonly ?string $unit = 'mL'
    ) {
        $this->max = ($min === $max) ? null : $max;
    }

    /**
     * @param DoseResult $other
     *
     * @return bool
     */
    public function equals(DoseResult $other): bool
    {
        return $this->min === $other->min
            && $this->max === $other->max
            && $this->unit === $other->unit;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (! isset($this->max) || $this->min === $this->max) {
            return trim("{$this->min} {$this->unit}");
        }

        return trim("{$this->min} - {$this->max} {$this->unit}");
    }
}
