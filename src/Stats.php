<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\MetaModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\StatModel;
use Lsv\BallDontLie\Utils\QueryOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Stats extends AbstractRequest
{
    /**
     * @param \DateTimeInterface[]|null                $dates
     * @param int[]|null                               $seasons
     * @param array<int|PlayerModel|AverageModel>|null $players
     * @param array<int|GameModel>|null                $games
     *
     * @return array{data: StatModel[], meta: MetaModel}
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function stats(
        int $page = 0,
        int $perPage = 25,
        array $dates = null,
        array $seasons = null,
        array $players = null,
        array $games = null,
        bool $postSeason = null,
        \DateTimeInterface $startDate = null,
        \DateTimeInterface $endDate = null,
    ): array {
        $options = new OptionsResolver();
        $options->setRequired(['page', 'per_page']);
        $options->setDefined(['dates', 'seasons', 'player_ids', 'game_ids', 'postseason', 'start_date', 'end_date']);

        $content = (new self())->request(
            'stats',
            new QueryOptions($options, [
                'page' => $page,
                'per_page' => $perPage,
                'dates' => self::datesMapper($dates),
                'seasons' => $seasons,
                'player_ids' => self::playersMapper($players),
                'game_ids' => self::gamesMapper($games),
                'postseason' => $postSeason,
                'start_date' => $startDate?->format('Y-m-d'),
                'end_date' => $endDate?->format('Y-m-d'),
            ]),
        );

        /** @var array{data: StatModel[], meta: MetaModel} $data */
        $data = self::deserializeData($content, StatModel::class, afterData: self::statsMapper());

        return $data;
    }
}
