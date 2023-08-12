<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;

final readonly class Mapper
{
    public static function gameMapper(int|GameModel $game): int
    {
        if ($game instanceof GameModel) {
            return $game->id;
        }

        return $game;
    }

    /**
     * @param array<int|GameModel>|null $games
     *
     * @return int[]|null
     */
    public static function gamesMapper(?array $games): ?array
    {
        if (!$games) {
            return null;
        }

        return array_map(static fn (int|GameModel $game): int => self::gameMapper($game), $games);
    }

    public static function playerMapper(int|PlayerModel|AverageModel $player): int
    {
        if ($player instanceof AverageModel) {
            return $player->playerId;
        }

        if ($player instanceof PlayerModel) {
            return $player->id;
        }

        return $player;
    }

    /**
     * @param array<int|PlayerModel|AverageModel>|null $players
     *
     * @return int[]|null
     */
    public static function playersMapper(?array $players): ?array
    {
        if (!$players) {
            return null;
        }

        return array_map(static fn (int|PlayerModel|AverageModel $player) => self::playerMapper($player), $players);
    }

    private static function dateMapper(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    /**
     * @param \DateTimeInterface[]|null $dates
     *
     * @return string[]|null
     */
    public static function datesMapper(?array $dates): ?array
    {
        return $dates ? array_map(static fn (\DateTimeInterface $date) => self::dateMapper($date), $dates) : null;
    }

    public static function teamMapper(TeamModel|PlayerModel|int $team): int
    {
        if ($team instanceof TeamModel) {
            return $team->id;
        }

        if ($team instanceof PlayerModel) {
            return $team->team->id;
        }

        return $team;
    }

    /**
     * @param array<int|TeamModel|PlayerModel>|null $teams
     *
     * @return int[]|null
     */
    public static function teamsMapper(?array $teams): ?array
    {
        if (!$teams) {
            return null;
        }

        return array_map(static fn (int|TeamModel|PlayerModel $team): int => self::teamMapper($team), $teams);
    }
}
