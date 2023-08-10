<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class AverageModel
{
    public int $playerId;
    public int $season;

    /**
     * @var SingleStat[]
     */
    public array $stats;
}
