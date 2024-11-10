<?php

declare(strict_types=1);

namespace App\Application\Content;

final readonly class ContentDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public string $imgUrl,
        public string $url,
    ) {}
}