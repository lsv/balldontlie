<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\AbstractRequest;
use Lsv\BallDontLie\Average;
use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\PlayerIdModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\StatEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class AverageTest extends TestCase
{
    private static MockHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = new MockHttpClient();
        Average::setClient(self::$client);
    }

    public function testAverage(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/average.json')));
        $content = Average::average();
        self::assertSame(AbstractRequest::BASE_URL.'/season_averages', Average::$url);
        self::assertSame([], Average::$query);
        self::assertCount(1, $content);

        self::assertSame(237, $content[0]->playerId);
        self::assertSame(2018, $content[0]->season);

        self::assertCount(20, $content[0]->stats);
        $values = [
            StatEnum::ast->name => 7.38,
            StatEnum::blk->name => 0.65,
            StatEnum::dreb->name => 7.59,
            StatEnum::fg3_pct->name => 0.358,
            StatEnum::fg3a->name => 5.73,
            StatEnum::fg3m->name => 2.05,
            StatEnum::fg_pct->name => 0.516,
            StatEnum::fga->name => 19.22,
            StatEnum::fgm->name => 9.92,
            StatEnum::ft_pct->name => 0.674,
            StatEnum::fta->name => 7.54,
            StatEnum::ftm->name => 5.08,
            StatEnum::min->name => '34:46',
            StatEnum::oreb->name => 0.95,
            StatEnum::pf->name => 1.59,
            StatEnum::pts->name => 26.97,
            StatEnum::reb->name => 8.54,
            StatEnum::stl->name => 1.32,
            StatEnum::turnover->name => 3.49,
            StatEnum::games_played->name => 37,
        ];

        foreach ($content[0]->stats as $stat) {
            if (array_key_exists($stat->key->name, $values)) {
                self::assertSame($values[$stat->key->name], $stat->value);
            } else {
                $this->fail("{$stat->key->name} is not in the values");
            }
        }
    }

    public static function attributeProvider(): \Generator
    {
        yield 'season' => [
            ['season' => 2018],
            ['season' => 2018],
        ];
        $playerModel = new PlayerModel();
        $playerModel->id = 2;

        $playerIdModel = new PlayerIdModel();
        $playerIdModel->id = 3;

        $avgModel = new AverageModel();
        $avgModel->playerId = 4;

        yield 'players' => [
            ['player_ids' => [1, 2, 3, 4]],
            ['players' => [1, $playerModel, $playerIdModel, $avgModel]],
        ];
    }

    /**
     * @dataProvider attributeProvider
     */
    public function testAverageAttributes(array $query, array $properties): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/average.json')));

        Average::average(...$properties);

        self::assertSame($query, Average::$query);
    }

    public function testMultiple(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/average_multi.json')));
        $content = Average::average();
        self::assertSame(AbstractRequest::BASE_URL.'/season_averages', Average::$url);
        self::assertCount(2, $content);
    }
}
