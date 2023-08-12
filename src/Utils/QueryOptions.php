<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

use Symfony\Component\OptionsResolver\OptionsResolver;

final readonly class QueryOptions
{
    /**
     * @param array<array-key, mixed> $values
     */
    public function __construct(
        public OptionsResolver $resolver,
        public array $values
    ) {
    }
}
