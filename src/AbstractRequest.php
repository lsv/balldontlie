<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\MetaModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;
use Lsv\BallDontLie\Utils\QueryOptions;
use Lsv\BallDontLie\Utils\Serializer;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractRequest
{
    public const BASE_URL = 'https://www.balldontlie.io/api/v1';
    private static ?HttpClientInterface $client = null;

    public static ?string $url = null;

    /**
     * @var array<mixed>|null
     */
    public static ?array $query = null;

    public static function setClient(HttpClientInterface $client): void
    {
        self::$client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function request(
        string $url,
        QueryOptions $queryOptions = null
    ): string {
        self::$query = [];
        if ($queryOptions) {
            self::$query = $queryOptions->resolver->resolve($queryOptions->values);
            self::$query = array_filter(self::$query, static fn (mixed $value) => !is_null($value));
        }

        self::$client = self::$client ?: HttpClient::create();
        $response = self::$client->request(
            $this->getHttpMethod(),
            $this->makeUrl($url),
            [
                'query' => self::$query,
            ]
        );

        return $response->getContent();
    }

    protected function getHttpMethod(): string
    {
        return 'GET';
    }

    private function makeUrl(string $url): string
    {
        return self::$url = sprintf('%s/%s', self::BASE_URL, $url);
    }

    /**
     * @param class-string $model
     *
     * @return array{data: mixed, meta: MetaModel|null}
     */
    protected static function deserializeData(string $content, string $model, bool $modelIsArray = true, callable $afterData = null): array
    {
        /** @var array<mixed> $decodedData */
        $decodedData = (new JsonDecode([JsonDecode::ASSOCIATIVE => true]))->decode($content, 'json');
        $encoder = new JsonEncode();

        $data = $decodedData;
        if (array_key_exists('data', $decodedData)) {
            $data = $decodedData['data'];
        }

        if ($afterData) {
            $data = $afterData($data, $modelIsArray);
        }

        $model = sprintf('%s%s', $model, $modelIsArray ? '[]' : '');
        $data = Serializer::getSerializer()->deserialize($encoder->encode($data, 'json'), $model, 'json');

        if (array_key_exists('meta', $decodedData)) {
            $decodedMeta = $decodedData['meta'];
            $meta = Serializer::getSerializer()->deserialize($encoder->encode($decodedMeta, 'json'), MetaModel::class, 'json');
        }

        return [
            'data' => $data,
            'meta' => $meta ?? null,
        ];
    }

    /**
     * @param \DateTimeInterface[]|null $dates
     *
     * @return string[]|null
     */
    protected static function datesMapper(?array $dates): ?array
    {
        return $dates ? array_map(static fn (\DateTimeInterface $date) => $date->format('Y-m-d'), $dates) : null;
    }

    /**
     * @param array<int|TeamModel|PlayerModel>|TeamModel|PlayerModel|int|null $teams
     *
     * @return int[]|int|null
     */
    protected static function teamsMapper(mixed $teams): null|array|int
    {
        if (!$teams) {
            return null;
        }

        $map = static function (int|TeamModel|PlayerModel $team): int {
            if ($team instanceof TeamModel) {
                return $team->id;
            }

            if ($team instanceof PlayerModel) {
                return $team->team->id;
            }

            return $team;
        };

        if (!is_array($teams)) {
            return $map($teams);
        }

        return array_map(static fn (int|TeamModel|PlayerModel $team): int => $map($team), $teams);
    }

    /**
     * @param array<int|PlayerModel|AverageModel>|int|PlayerModel|AverageModel|null $players
     *
     * @return int[]|int|null
     */
    protected static function playersMapper(mixed $players): null|int|array
    {
        if (!$players) {
            return null;
        }

        $map = static function (int|PlayerModel|AverageModel $player): int {
            if ($player instanceof AverageModel) {
                return $player->playerId;
            }

            if ($player instanceof PlayerModel) {
                return $player->id;
            }

            return $player;
        };

        if (!is_array($players)) {
            return $map($players);
        }

        return array_map(static fn (int|PlayerModel|AverageModel $player) => $map($player), $players);
    }

    /**
     * @param array<int|GameModel>|int|GameModel|null $games
     *
     * @return int[]|int|null
     */
    protected static function gamesMapper(mixed $games): mixed
    {
        if (!$games) {
            return null;
        }

        $map = static function (int|GameModel $game): int {
            if ($game instanceof GameModel) {
                return $game->id;
            }

            return $game;
        };

        if (!is_array($games)) {
            return $map($games);
        }

        return array_map(static fn (int|GameModel $game): int => $map($game), $games);
    }

    protected static function statsMapper(): callable
    {
        return static function (array $data) {
            foreach ($data as &$dataItem) {
                foreach ($dataItem as $key => $item) {
                    if (!in_array($key, ['game', 'player', 'team', 'id', 'player_id', 'season'], true)) {
                        $dataItem['stats'][] = ['key' => $key, 'value' => $item];
                        unset($dataItem[$key]);
                    }
                }
            }
            unset($dataItem);

            return $data;
        };
    }
}
