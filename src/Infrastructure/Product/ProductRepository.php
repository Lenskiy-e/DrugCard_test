<?php
declare(strict_types=1);

namespace App\Infrastructure\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

final readonly class ProductRepository implements ProductRepositoryInterface
{
    private ObjectRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->entityManager = $registry->getManager();
        $this->repository = $registry->getRepository(Product::class);
    }

    public function find(int $id): Product
    {
        $product = $this->repository->find($id);

        if (null === $product) {
            throw new EntityNotFoundException(sprintf('Product with id "%d" does not exist.', $id));
        }

        return $product;
    }

    public function findAll(int $page, int $limit): Paginator
    {
        $qb = $this->repository
            ->createQueryBuilder('p')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('p.id', Order::Ascending->value);

        return new Paginator($qb);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}