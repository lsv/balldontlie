<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

final readonly class Convert
{
    public static function poundsToKilograms(float|int $pounds): float
    {
        return round($pounds / 2.205, 2);
    }

    public static function feetToCentimeters(int $feet, int $inches): int
    {
        return (int) floor(2.54 * ($feet * 12 + $inches));
    }
}
