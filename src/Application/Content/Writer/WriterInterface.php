<?php

declare(strict_types=1);

namespace App\Application\Content\Writer;

interface WriterInterface
{
    public function write(array $headers, array $content): void;
}