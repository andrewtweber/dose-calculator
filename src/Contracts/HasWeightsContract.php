<?php

namespace DoseCalculator\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface HasWeightsContract
 *
 * @package DoseCalculator\Contracts
 */
interface HasWeightsContract
{
    /**
     * @return HasMany
     */
    public function weights(): HasMany;
}
