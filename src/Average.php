<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Utils\Mapper;
use Lsv\BallDontLie\Utils\QueryOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class Average extends AbstractRequest
{
    /**
     * @param array<int|PlayerModel>|null $players
     *
     * @return AverageModel[]
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function average(
        int $season = null,
        array $players = null,
    ): array {
        $options = new OptionsResolver();
        $options->setDefined(['season', 'player_ids']);
        $content = (new self())->request(
            'season_averages',
            new QueryOptions($options, [
                'season' => $season,
                'player_ids' => Mapper::playersMapper($players),
            ]),
        );

        /** @var AverageModel[] $data */
        $data = self::deserializeData($content, AverageModel::class, afterData: self::statsMapper())['data'];

        return $data;
    }
}
