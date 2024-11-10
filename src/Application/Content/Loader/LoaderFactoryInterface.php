<?php

declare(strict_types=1);

namespace App\Application\Content\Loader;

interface LoaderFactoryInterface
{
    public function get(LoaderType $type): LoaderInterface;
}