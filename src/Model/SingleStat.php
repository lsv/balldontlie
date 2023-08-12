<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

use Lsv\BallDontLie\Utils\StatEnum;

class SingleStat
{
    public StatEnum $key;
    public mixed $value;

    public function name(): string
    {
        return StatEnum::name($this->key);
    }
}
