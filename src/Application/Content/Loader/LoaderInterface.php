<?php

declare(strict_types=1);

namespace App\Application\Content\Loader;

use App\Application\Content\ContentDTO;

interface LoaderInterface
{
    /**
     * @return ContentDTO[]
     */
    public function load(string $url): array;
}