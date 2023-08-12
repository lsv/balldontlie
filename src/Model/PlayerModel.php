<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

use Lsv\BallDontLie\Utils\Convert;

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

    public function heightCentimeters(): ?int
    {
        if (null === $this->heightFeet || null === $this->heightInches) {
            return null;
        }

        return Convert::feetToCentimeters($this->heightFeet, $this->heightInches);
    }

    public function weightKilograms(): ?float
    {
        if (null === $this->weightPounds) {
            return null;
        }

        return Convert::poundsToKilograms($this->weightPounds);
    }
}
