<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Model;

class MetaModel
{
    public int $totalPages;
    public int $currentPage;
    public ?int $nextPage = null;
    public int $perPage;
    public int $totalCount;
}
