<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\AbstractRequest;
use Lsv\BallDontLie\Model\PlayerModel;
use Lsv\BallDontLie\Model\TeamModel;
use Lsv\BallDontLie\Team;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class TeamTest extends TestCase
{
    private static MockHttpClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        self::$client = new MockHttpClient();
        Team::setClient(self::$client);
    }

    public function testTeam(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/team.json')));

        $content = Team::team(14);
        self::assertSame(AbstractRequest::BASE_URL.'/teams/14', Team::$url);
        self::assertSame([], Team::$query);
        $this->assertContent($content);
    }

    public function testTeamByTeamModel(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/team.json')));
        $team = new TeamModel();
        $team->id = 14;

        Team::team($team);
        self::assertSame(AbstractRequest::BASE_URL.'/teams/14', Team::$url);
    }

    public function testTeamByPlayerModel(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/team.json')));
        $team = new PlayerModel();
        $team->team = new TeamModel();
        $team->team->id = 14;

        Team::team($team);
        self::assertSame(AbstractRequest::BASE_URL.'/teams/14', Team::$url);
    }

    public function testTeams(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/teams.json')));

        $content = Team::teams();
        self::assertSame(['page' => 0, 'per_page' => 25], Team::$query);
        self::assertCount(1, $content['data']);
        $this->assertContent($content['data'][0]);

        $meta = $content['meta'];
        self::assertSame(1, $meta->totalPages);
        self::assertSame(1, $meta->currentPage);
        self::assertNull($meta->nextPage);
        self::assertSame(30, $meta->perPage);
        self::assertSame(30, $meta->totalCount);
    }

    public function testTeamsPagination(): void
    {
        self::$client->setResponseFactory(new MockResponse(file_get_contents(__DIR__.'/responses/teams.json')));

        Team::teams(1, 50);
        self::assertSame(['page' => 1, 'per_page' => 50], Team::$query);
    }

    private function assertContent(TeamModel $content): void
    {
        self::assertSame(14, $content->id);
        self::assertSame('LAL', $content->abbreviation);
        self::assertSame('Los Angeles', $content->city);
        self::assertSame('West', $content->conference);
        self::assertSame('Pacific', $content->division);
        self::assertSame('Los Angeles Lakers', $content->fullName);
        self::assertSame('Lakers', $content->name);
    }
}
