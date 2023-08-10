<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class StatModel
{
    public int $id;

    /**
     * @var SingleStat[]
     */
    public array $stats;

    public PlayerIdModel $player;
    public GameIdModel $game;
    public TeamModel $team;
}
