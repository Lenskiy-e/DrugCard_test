<?php

declare(strict_types=1);

namespace App\Domain\Product;

use Doctrine\ORM\Tools\Pagination\Paginator;

interface ProductRepositoryInterface
{
    public function find(int $id): Product;

    public function findAll(int $page, int $limit): Paginator;

    public function save(Product $product): void;
}