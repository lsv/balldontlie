<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class GameModel
{
    public int $id;
    public \DateTimeInterface $date;
    public ?int $homeTeamScore;
    public ?int $visitorTeamScore;
    public int $season;
    public int $period;
    public string $status;
    public ?string $time;
    public bool $postseason;
    public TeamModel $homeTeam;
    public TeamModel $visitorTeam;
}
