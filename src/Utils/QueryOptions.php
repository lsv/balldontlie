<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

use Symfony\Component\OptionsResolver\OptionsResolver;

readonly class QueryOptions
{
    /**
     * @param array<mixed> $values
     */
    public function __construct(
        public OptionsResolver $resolver,
        public array $values
    ) {
    }
}
