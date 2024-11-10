<?php

declare(strict_types=1);

namespace App\Application\Product\Query\GetCollection;

final readonly class GetCollectionQuery
{
    public function __construct(
        public int $page,
        public int $limit,
    ) {}
}