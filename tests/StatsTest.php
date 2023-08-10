<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\AbstractRequest;
use Lsv\BallDontLie\Model\AverageModel;
use Lsv\BallDontLie\Model\GameIdModel;
use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\PlayerIdModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\StatEnum;
use Lsv\BallDontLie\Stats;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class StatsTest extends TestCase
{
    private static MockHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = new MockHttpClient();
        Stats::setClient(self::$client);
    }

    public function testStats(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/stats.json')));
        $content = Stats::stats();
        self::assertSame(AbstractRequest::BASE_URL.'/stats', Stats::$url);
        self::assertSame(['page' => 0, 'per_page' => 25], Stats::$query);
        self::assertCount(1, $content['data']);
        $model = $content['data'][0];

        self::assertSame(1, $model->game->id);
        self::assertSame('2018-10-16 00:00:00', $model->game->date->format('Y-m-d H:i:s'));
        self::assertSame(2, $model->game->homeTeamId);
        self::assertSame(105, $model->game->homeTeamScore);
        self::assertSame(2018, $model->game->season);
        self::assertSame(23, $model->game->visitorTeamId);
        self::assertSame(87, $model->game->visitorTeamScore);

        self::assertSame(145, $model->player->id);
        self::assertSame('Joel', $model->player->firstName);
        self::assertSame('Embiid', $model->player->lastName);
        self::assertSame('F-C', $model->player->position);
        self::assertSame(23, $model->player->teamId);

        self::assertSame(23, $model->team->id);
        self::assertSame('PHI', $model->team->abbreviation);
        self::assertSame('Philadelphia', $model->team->city);
        self::assertSame('East', $model->team->conference);
        self::assertSame('Atlantic', $model->team->division);
        self::assertSame('Philadelphia 76ers', $model->team->fullName);
        self::assertSame('76ers', $model->team->name);

        self::assertCount(19, $model->stats);
        $values = [
            StatEnum::ast->name => 2,
            StatEnum::blk->name => 2,
            StatEnum::dreb->name => 8,
            StatEnum::fg3_pct->name => 0.25,
            StatEnum::fg3a->name => 4,
            StatEnum::fg3m->name => 1,
            StatEnum::fg_pct->name => 0.429,
            StatEnum::fga->name => 21,
            StatEnum::fgm->name => 9,
            StatEnum::ft_pct->name => 0.8,
            StatEnum::fta->name => 5,
            StatEnum::ftm->name => 4,
            StatEnum::min->name => '36:49',
            StatEnum::oreb->name => 2,
            StatEnum::pf->name => 3,
            StatEnum::pts->name => 23,
            StatEnum::reb->name => 10,
            StatEnum::stl->name => 1,
            StatEnum::turnover->name => 5,
        ];

        foreach ($model->stats as $stat) {
            if (array_key_exists($stat->key->name, $values)) {
                self::assertSame($values[$stat->key->name], $stat->value);
            } else {
                $this->fail("{$stat->key->name} is not in the values");
            }
        }

        $meta = $content['meta'];
        self::assertSame(2042, $meta->totalPages);
        self::assertSame(1, $meta->currentPage);
        self::assertSame(2, $meta->nextPage);
        self::assertSame(25, $meta->perPage);
        self::assertSame(51045, $meta->totalCount);
    }

    public static function attributesProvider(): \Generator
    {
        yield 'dates' => [
            ['dates' => ['2023-01-02', '2022-03-18']],
            ['dates' => [new \DateTime('2023-01-02'), new \DateTime('2022-03-18')]],
        ];
        yield 'seasons' => [
            ['seasons' => [2018, 2019, 2020]],
            ['seasons' => [2018, 2019, 2020]],
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

        $gameModel = new GameModel();
        $gameModel->id = 2;

        $gameIdModel = new GameIdModel();
        $gameIdModel->id = 3;

        yield 'games' => [
            ['game_ids' => [1, 2, 3]],
            ['games' => [1, $gameModel, $gameIdModel]],
        ];
        yield 'postseason' => [
            ['postseason' => false],
            ['postSeason' => false],
        ];
        yield 'startdate' => [
            ['start_date' => '2023-02-18'],
            ['startDate' => new \DateTime('2023-02-18')],
        ];
        yield 'enddate' => [
            ['end_date' => '2023-02-18'],
            ['endDate' => new \DateTime('2023-02-18')],
        ];
    }

    /**
     * @dataProvider attributesProvider
     */
    public function testStatsAttributes(array $query, array $properties): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/stats.json')));
        Stats::stats(...$properties);
        self::assertSame(array_merge(['page' => 0, 'per_page' => 25], $query), Stats::$query);
    }
}
