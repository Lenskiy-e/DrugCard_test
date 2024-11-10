<?php

declare(strict_types=1);

namespace App\Infrastructure\Content\Loader;

use App\Application\Content\Loader\LoaderFactoryInterface;
use App\Application\Content\Loader\LoaderType;

final readonly class LoaderFactory implements LoaderFactoryInterface
{
    public function get(LoaderType $type): AbstractLoader
    {
        return match ($type) {
            LoaderType::ALLO => new AlloLoader(),
            LoaderType::COMFY => throw new \Exception('To be implemented'),
            LoaderType::FOXTROT => throw new \Exception('To be implemented'),
        };
    }
}