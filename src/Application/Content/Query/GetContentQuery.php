<?php

declare(strict_types=1);

namespace App\Application\Content\Query;

use App\Application\Content\Loader\LoaderType;

final readonly class GetContentQuery
{
 public function __construct(
     public string $url,
     public LoaderType $type,
 ) {}
}