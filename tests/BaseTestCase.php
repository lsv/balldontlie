<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest;

use Lsv\BallDontLie\AbstractRequest;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static function assertUrl(string $exceptedUrl, string $actualUrl, array $query = []): void
    {
        $exceptedUrl = sprintf('%s/%s', AbstractRequest::BASE_URL, $exceptedUrl);
        $parsed = parse_url($actualUrl);
        if (!$query) {
            $actualUrl = sprintf('%s://%s%s', $parsed['scheme'], $parsed['host'], $parsed['path']);
        } else {
            $queryBuild = http_build_query($query);
            $queryBuild = preg_replace('/%5B\d+%5D/imU', '%5B%5D', $queryBuild);
            $exceptedUrl = sprintf('%s?%s', $exceptedUrl, $queryBuild);
        }
        self::assertSame($exceptedUrl, $actualUrl);
    }
}
