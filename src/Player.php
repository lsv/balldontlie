<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\MetaModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Utils\QueryOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Player extends AbstractRequest
{
    /**
     * @return array{data: PlayerModel[], meta: MetaModel}
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function players(int $page = 0, int $perPage = 25, string $search = null): array
    {
        $options = new OptionsResolver();
        $options->setRequired(['page', 'per_page']);
        $options->setDefined(['search']);

        $content = (new self())->request(
            'players',
            new QueryOptions($options, [
                'page' => $page,
                'per_page' => $perPage,
                'search' => $search,
            ]),
        );

        /** @var array{data: PlayerModel[], meta: MetaModel} $data */
        $data = self::deserializeData($content, PlayerModel::class);

        return $data;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function player(int|PlayerModel|AverageModel $id): PlayerModel
    {
        $content = (new self())->request(
            sprintf('players/%d', (int) self::playersMapper($id))
        );

        /** @var PlayerModel $data */
        $data = self::deserializeData($content, PlayerModel::class, false)['data'];

        return $data;
    }
}
