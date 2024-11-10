<?php

declare(strict_types=1);

namespace App\Application\Product\Command\Create;

use App\Application\Content\ContentDTO;

final readonly class CreateCommand
{
    public function __construct(
        public ContentDTO $content,
    ) {}
}