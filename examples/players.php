<?php

use Lsv\BallDontLie\Player;
use Lsv\BallDontLie\Stats;

require __DIR__ . '/../vendor/autoload.php';

$stats = Stats::stats(players: [237]);
var_dump($stats);