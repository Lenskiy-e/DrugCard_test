<?php

declare(strict_types=1);

namespace App\Application\Content\Loader;

enum LoaderType: string
{
    case COMFY = 'comfy';
    case ALLO = 'allo';
    case FOXTROT = 'foxtrot';
}
