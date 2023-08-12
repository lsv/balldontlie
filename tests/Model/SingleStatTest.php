<?php

declare(strict_types=1);

namespace Lsv\BallDontLieTest\Model;

use Lsv\BallDontLie\Model\SingleStat;
use Lsv\BallDontLie\Utils\StatEnum;
use PHPUnit\Framework\TestCase;

class SingleStatTest extends TestCase
{
    public function testName(): void
    {
        $stat = new SingleStat();
        $stat->key = StatEnum::ast;
        self::assertSame('assist', $stat->name());
    }
}
