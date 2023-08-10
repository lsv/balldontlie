<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class PlayerModel
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $position;
    public ?int $heightFeet;
    public ?int $heightInches;
    public ?int $weightPounds;
    public TeamModel $team;
}
