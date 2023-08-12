<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

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

final class Team extends AbstractRequest
{
    /**
     * @return array{data: TeamModel[], meta: MetaModel}
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function teams(int $page = 0, int $perPage = 25): array
    {
        $options = new OptionsResolver();
        $options->setRequired(['page', 'per_page']);

        $content = (new self())->request(
            'teams',
            new QueryOptions($options, [
                'page' => $page,
                'per_page' => $perPage,
            ]),
        );

        /** @var array{data: TeamModel[], meta: MetaModel} $data */
        $data = self::deserializeData($content, TeamModel::class);

        return $data;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function team(int|PlayerModel|TeamModel $id): TeamModel
    {
        $content = (new self())->request(
            sprintf('teams/%d', Mapper::teamMapper($id))
        );

        /** @var TeamModel $data */
        $data = self::deserializeData($content, TeamModel::class, false)['data'];

        return $data;
    }
}
