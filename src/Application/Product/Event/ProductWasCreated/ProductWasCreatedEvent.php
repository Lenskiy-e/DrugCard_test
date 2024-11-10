<?php
declare(strict_types=1);

namespace App\Application\Product\Event\ProductWasCreated;

final readonly class ProductWasCreatedEvent
{
    public function __construct(
        public int $productId,
    ) {}
}