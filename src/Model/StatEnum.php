<?php

/** @noinspection SpellCheckingInspection */
declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

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
}
