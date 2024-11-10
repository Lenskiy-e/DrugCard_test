<?php

declare(strict_types=1);

namespace App\Application\Product\Query\GetCollection;

use App\Application\Product\Exception\ValidationException;
use App\Domain\Product\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCollectionHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(GetCollectionQuery $query): array
    {
        if ($query->page < 0) {
            throw new ValidationException('Parameter page must be greater or equal 0');
        }

        if ($query->limit < 1) {
            throw new ValidationException('Parameter limit must be greater or equal 1');
        }

        $paginator = $this->productRepository->findAll($query->page, $query->limit);
        $products = [];

        foreach ($paginator as $product) {
            $products[] = $product->getData();
        }

        return [
            'total_count' => $paginator->count(),
            'products' => $products,
        ];
    }
}