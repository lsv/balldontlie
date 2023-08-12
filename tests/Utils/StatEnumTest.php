<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest\Utils;

use Lsv\BallDontLie\Utils\StatEnum;
use PHPUnit\Framework\TestCase;

class StatEnumTest extends TestCase
{
    public static function nameProvider(): \Generator
    {
        yield [StatEnum::ast, 'assist'];
        yield [StatEnum::blk, 'block'];
        yield [StatEnum::stl, 'steals'];
        yield [StatEnum::reb, 'rebounds'];
        yield [StatEnum::pts, 'points'];
        yield [StatEnum::oreb, 'offensive rebounds'];
        yield [StatEnum::dreb, 'defensive rebounds'];
        yield [StatEnum::ft_pct, 'free throws percentage'];
        yield [StatEnum::fta, 'free throws attempts'];
        yield [StatEnum::fg3a, '3 points attempts'];
        yield [StatEnum::fg3_pct, '3 points percentage'];
        yield [StatEnum::fg3m, '3 points made'];
        yield [StatEnum::min, 'minutes'];
        yield [StatEnum::ftm, 'free throws made'];
        yield [StatEnum::fg_pct, 'field goal percentage'];
        yield [StatEnum::fga, 'field goal attempts'];
        yield [StatEnum::fgm, 'field goal made'];
        yield [StatEnum::pf, 'rating'];
        yield [StatEnum::turnover, 'turnovers'];
        yield [StatEnum::games_played, 'games played'];
    }

    /**
     * @dataProvider nameProvider
     */
    public function testName(StatEnum $key, string $expected): void
    {
        self::assertSame($expected, StatEnum::name($key));
    }
}
