<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest\Model;

use Lsv\BallDontLie\Model\PlayerModel;
use PHPUnit\Framework\TestCase;

class PlayerModelTest extends TestCase
{
    public function testHeightCentimeters(): void
    {
        $player = new PlayerModel();
        $player->heightInches = 8;
        $player->heightFeet = 6;
        self::assertSame(203, $player->heightCentimeters());
    }

    public function testEmptyHeightCentimeters(): void
    {
        $player = new PlayerModel();
        $player->heightInches = null;
        $player->heightFeet = 1;
        self::assertNull($player->heightCentimeters());
    }

    public function testWeightKilograms(): void
    {
        $player = new PlayerModel();
        $player->weightPounds = 201;
        self::assertSame(91.16, $player->weightKilograms());
    }

    public function testEmptyWeightKilograms(): void
    {
        $player = new PlayerModel();
        $player->weightPounds = null;
        self::assertNull($player->weightKilograms());
    }
}
