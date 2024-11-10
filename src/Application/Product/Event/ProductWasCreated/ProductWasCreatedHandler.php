<?php

declare(strict_types=1);

namespace App\Application\Product\Event\ProductWasCreated;

use App\Application\Content\Exception\ContentWriteException;
use App\Application\Content\Writer\WriterInterface;
use App\Domain\Product\ProductRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductWasCreatedHandler
{
    public function __construct(
        private ProductRepositoryInterface $repository,
        private LoggerInterface $logger,
        private WriterInterface $csvWriter,
    ) {}

    public function __invoke(ProductWasCreatedEvent $event): void
    {
        try {
            $product = $this->repository->find($event->productId);
            $this->csvWriter->write(array_keys($product->getData()), $product->getData());
        } catch (EntityNotFoundException|ContentWriteException) {
            // handling logic according to the business requirements
        } catch (\Throwable $e) {
            $this->logger->error('Error handling ProductWasCreatedEvent', [
                'message' => $e->getMessage(),
            ]);

            // if we need to retry, throw a new exception
        }
    }
}