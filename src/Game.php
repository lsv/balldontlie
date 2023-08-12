<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\MetaModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;
use Lsv\BallDontLie\Utils\Mapper;
use Lsv\BallDontLie\Utils\QueryOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class Game extends AbstractRequest
{
    /**
     * @param \DateTimeInterface[]|null             $dates
     * @param int[]|null                            $seasons
     * @param array<int|TeamModel|PlayerModel>|null $teams
     *
     * @return array{data: GameModel[], meta: MetaModel}
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function games(
        array $dates = null,
        array $seasons = null,
        array $teams = null,
        bool $postSeason = null,
        \DateTimeInterface $startDate = null,
        \DateTimeInterface $endDate = null,
        int $page = 0,
        int $perPage = 25,
    ): array {
        $options = new OptionsResolver();
        $options->setRequired(['page', 'per_page']);
        $options->setDefined(['dates', 'seasons', 'team_ids', 'postseason', 'start_date', 'end_date']);

        $content = (new self())->request(
            'games',
            new QueryOptions($options, [
                'page' => $page,
                'per_page' => $perPage,
                'dates' => Mapper::datesMapper($dates),
                'seasons' => $seasons,
                'team_ids' => Mapper::teamsMapper($teams),
                'postseason' => $postSeason,
                'start_date' => $startDate?->format('Y-m-d'),
                'end_date' => $endDate?->format('Y-m-d'),
            ]),
        );

        /** @var array{data: GameModel[], meta: MetaModel} $data */
        $data = self::deserializeData($content, GameModel::class);

        return $data;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function game(int|GameModel $game): GameModel
    {
        $content = (new self())->request(
            sprintf('games/%d', Mapper::gameMapper($game)),
        );

        /** @var GameModel $data */
        $data = self::deserializeData($content, GameModel::class, false)['data'];

        return $data;
    }
}
