<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;
use Lsv\BallDontLie\Player;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class PlayerTest extends BaseTestCase
{
    private static MockHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = new MockHttpClient();
        Player::setClient(self::$client);
    }

    public function testPlayer(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/player.json')));

        $content = Player::player(237);
        self::assertUrl('players/237', Player::$url);
        self::assertSame([], Player::$query);
        $this->assertContent($content);
    }

    public function testPlayerByModel(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/player.json')));
        $player = new PlayerModel();
        $player->id = 237;

        $content = Player::player($player);
        self::assertUrl('players/237', Player::$url);
        $this->assertContent($content);
    }

    public function testPlayers(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/players.json')));

        $content = Player::players();
        self::assertUrl('players', Player::$url);
        self::assertSame(['page' => 0, 'per_page' => 25], Player::$query);
        self::assertCount(1, $content['data']);
        $this->assertContent($content['data'][0]);

        $meta = $content['meta'];
        self::assertSame(50, $meta->totalPages);
        self::assertSame(1, $meta->currentPage);
        self::assertSame(2, $meta->nextPage);
        self::assertSame(25, $meta->perPage);
        self::assertSame(9999, $meta->totalCount);
    }

    public function testPlayersAttributes(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/players.json')));

        Player::players('hello world', 5, 100);
        self::assertSame(['page' => 5, 'per_page' => 100, 'search' => 'hello world'], Player::$query);
    }

    private function assertContent(object $content): void
    {
        self::assertSame(237, $content->id);
        self::assertSame('LeBron', $content->firstName);
        self::assertSame('James', $content->lastName);
        self::assertSame('F', $content->position);
        self::assertSame(6, $content->heightFeet);
        self::assertSame(8, $content->heightInches);
        self::assertSame(250, $content->weightPounds);
        self::assertInstanceOf(TeamModel::class, $content->team);
        self::assertSame(14, $content->team->id);
        self::assertSame('LAL', $content->team->abbreviation);
        self::assertSame('Los Angeles', $content->team->city);
        self::assertSame('West', $content->team->conference);
        self::assertSame('Pacific', $content->team->division);
        self::assertSame('Los Angeles Lakers', $content->team->fullName);
        self::assertSame('Lakers', $content->team->name);
    }
}
