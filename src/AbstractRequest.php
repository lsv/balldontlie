<?php

declare(strict_types=1);

namespace Lsv\BallDontLie;

use Lsv\BallDontLie\Model\MetaModel;
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

    public static string $url;

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
    protected function request(
        string $url,
        QueryOptions $queryOptions = null
    ): string {
        self::$query = [];
        if ($queryOptions) {
            self::$query = $queryOptions->resolver->resolve($queryOptions->values);
            self::$query = array_filter(self::$query, static fn (mixed $value) => !is_null($value));
        }

        $this->makeUrl($url);

        self::$client = self::$client ?: HttpClient::create();
        $response = self::$client->request(
            $this->getHttpMethod(),
            self::$url,
        );

        return $response->getContent();
    }

    private function getHttpMethod(): string
    {
        return 'GET';
    }

    private function makeUrl(string $url): void
    {
        self::$url = sprintf('%s/%s', self::BASE_URL, $url);
        if (self::$query) {
            $query = http_build_query(self::$query);
            $query = preg_replace('/%5B\d+%5D/imU', '%5B%5D', $query);
            self::$url .= '?'.$query;
        }
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
