<?php

/** @noinspection SpellCheckingInspection */
declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

enum StatEnum: string
{
    case ast = 'ast';
    case blk = 'blk';
    case dreb = 'dreb';
    case fg3_pct = 'fg3_pct';
    case fg3a = 'fg3a';
    case fg3m = 'fg3m';
    case fg_pct = 'fg_pct';
    case fga = 'fga';
    case fgm = 'fgm';
    case ft_pct = 'ft_pct';
    case fta = 'fta';
    case ftm = 'ftm';
    case min = 'min';
    case oreb = 'oreb';
    case pf = 'pf';
    case pts = 'pts';
    case reb = 'reb';
    case stl = 'stl';
    case turnover = 'turnover';
    case games_played = 'games_played';

    public static function name(StatEnum $stat): string
    {
        return match ($stat) {
            self::ast => 'assist',
            self::blk => 'block',
            self::stl => 'steals',
            self::reb => 'rebounds',
            self::pts => 'points',
            self::oreb => 'offensive rebounds',
            self::dreb => 'defensive rebounds',
            self::ft_pct => 'free throws percentage',
            self::fta => 'free throws attempts',
            self::fg3a => '3 points attempts',
            self::fg3_pct => '3 points percentage',
            self::fg3m => '3 points made',
            self::min => 'minutes',
            self::ftm => 'free throws made',
            self::fg_pct => 'field goal percentage',
            self::fga => 'field goal attempts',
            self::fgm => 'field goal made',
            self::pf => 'rating',
            self::turnover => 'turnovers',
            self::games_played => 'games played',
        };
    }
}
