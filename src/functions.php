<?php

if (! function_exists('bcnegative')) {
    /**
     * @param mixed $number
     *
     * @return bool
     */
    function bcnegative(mixed $number): bool
    {
        return str_starts_with($number, '-');
    }
}

if (! function_exists('bcceil')) {
    /**
     * @param mixed $number
     *
     * @return string
     */
    function bcceil(mixed $number): string
    {
        return bcnegative($number)
            ? (($v = bcfloor(substr($number, 1))) ? "-$v" : $v)
            : bcadd(strtok($number, '.'), strtok('.') != 0);
    }
}

if (! function_exists('bcfloor')) {
    /**
     * @param mixed $number
     *
     * @return string
     */
    function bcfloor(mixed $number): string
    {
        return bcnegative($number)
            ? '-' . bcceil(substr($number, 1))
            : strtok($number, '.');
    }
}

if (! function_exists('bcround')) {
    /**
     * @param mixed $number
     * @param int   $precision
     *
     * @return string
     */
    function bcround(mixed $number, int $precision = 0): string
    {
        $e = bcpow(10, $precision + 1);

        return bcdiv(bcadd(bcmul($number, $e, 0), bcnegative($number) ? -5 : 5), $e, $precision);
    }
}
