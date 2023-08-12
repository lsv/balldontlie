<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest\Utils;

use Lsv\BallDontLie\Utils\Convert;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    public static function feetProvider(): \Generator
    {
        yield [1, 1, 33];
        yield [2, 9, 83];
        yield [3, 5, 104];
        yield [4, 7, 139];
        yield [5, 6, 167];
        yield [6, 7, 200];
        yield [6, 6, 198];
        yield [5, 9, 175];
    }

    /**
     * @dataProvider feetProvider
     */
    public function testFeetToCentimeters(int $feet, int $inches, int $centimeters): void
    {
        self::assertSame($centimeters, Convert::feetToCentimeters($feet, $inches));
    }

    public function testPoundsToKilograms(): void
    {
        self::assertSame(90.7, Convert::poundsToKilograms(200));
    }
}
