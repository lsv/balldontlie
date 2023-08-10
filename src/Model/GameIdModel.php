<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class GameIdModel extends GameModel
{
    public int $homeTeamId;
    public int $visitorTeamId;
}
