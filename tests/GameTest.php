<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\AbstractRequest;
use Lsv\BallDontLie\Game;
use Lsv\BallDontLie\Model\GameIdModel;
use Lsv\BallDontLie\Model\GameModel;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GameTest extends BaseTestCase
{
    private static MockHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = new MockHttpClient();
        Game::setClient(self::$client);
    }

    public function testGames(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/games.json')));

        $content = Game::games();
        self::assertUrl('games', Game::$url, ['page' => 0, 'per_page' => 25]);
        self::assertSame(['page' => 0, 'per_page' => 25], Game::$query);
        self::assertCount(1, $content['data']);
        $this->assertContent($content['data'][0]);

        $meta = $content['meta'];
        self::assertSame(1877, $meta->totalPages);
        self::assertSame(1, $meta->currentPage);
        self::assertSame(2, $meta->nextPage);
        self::assertSame(25, $meta->perPage);
        self::assertSame(46911, $meta->totalCount);
    }

    public static function attributeProvider(): \Generator
    {
        yield 'dates' => [
            ['dates' => ['2023-01-02', '2022-03-18']],
            ['dates' => [new \DateTime('2023-01-02'), new \DateTime('2022-03-18')]],
        ];
        yield 'seasons' => [
            ['seasons' => [2018, 2019, 2020]],
            ['seasons' => [2018, 2019, 2020]],
        ];

        $teamModel = new TeamModel();
        $teamModel->id = 2;

        $playerModel = new PlayerModel();
        $playerModel->team = new TeamModel();
        $playerModel->team->id = 3;

        yield 'teams' => [
            ['team_ids' => [1, 2, 3]],
            ['teams' => [1, $teamModel, $playerModel]],
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

        yield 'mixed' => [
            ['team_ids' => [1, 2, 3], 'start_date' => '2023-02-18', 'end_date' => '2023-02-18'],
            ['teams' => [1, $teamModel, $playerModel], 'endDate' => new \DateTime('2023-02-18'), 'startDate' => new \DateTime('2023-02-18')],
        ];
    }

    /**
     * @dataProvider attributeProvider
     */
    public function testGamesAttributes(array $query, array $properties): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/games.json')));

        Game::games(...$properties);

        self::assertSame(array_merge(['page' => 0, 'per_page' => 25], $query), Game::$query);
    }

    public function testGame(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/game.json')));

        $content = Game::game(1);
        self::assertSame(AbstractRequest::BASE_URL.'/games/1', Game::$url);
        $this->assertContent($content);
    }

    public function testGameByGameModel(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/game.json')));

        $model = new GameModel();
        $model->id = 1;

        $content = Game::game($model);
        self::assertSame(AbstractRequest::BASE_URL.'/games/1', Game::$url);
        $this->assertContent($content);
    }

    public function testGameByGameIdModel(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/game.json')));

        $model = new GameIdModel();
        $model->id = 1;

        $content = Game::game($model);
        self::assertSame(AbstractRequest::BASE_URL.'/games/1', Game::$url);
        $this->assertContent($content);
    }

    private function assertContent(GameModel $model): void
    {
        self::assertSame(1, $model->id);
        self::assertSame('2018-10-16 00:00:00', $model->date->format('Y-m-d H:i:s'));
        self::assertSame(105, $model->homeTeamScore);
        self::assertSame(87, $model->visitorTeamScore);
        self::assertSame(2018, $model->season);
        self::assertSame(4, $model->period);
        self::assertSame('Final', $model->status);
        self::assertSame(' ', $model->time);
        self::assertFalse($model->postseason);

        $content = $model->homeTeam;
        self::assertSame(2, $content->id);
        self::assertSame('BOS', $content->abbreviation);
        self::assertSame('Boston', $content->city);
        self::assertSame('East', $content->conference);
        self::assertSame('Atlantic', $content->division);
        self::assertSame('Boston Celtics', $content->fullName);
        self::assertSame('Celtics', $content->name);

        $content = $model->visitorTeam;
        self::assertSame(23, $content->id);
        self::assertSame('PHI', $content->abbreviation);
        self::assertSame('Philadelphia', $content->city);
        self::assertSame('East', $content->conference);
        self::assertSame('Atlantic', $content->division);
        self::assertSame('Philadelphia 76ers', $content->fullName);
        self::assertSame('76ers', $content->name);
    }
}
