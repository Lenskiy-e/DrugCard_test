<?php

declare(strict_types=1);

namespace App\Application\Product\Command\Create;

use App\Application\Product\Event\ProductWasCreated\ProductWasCreatedEvent;
use App\Application\Product\Exception\ProductCreatingException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class CreateHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ProductCreatingException
     */
    public function __invoke(CreateCommand $command): void
    {
        try {
            $product = Product::create(
                name: $command->content->name,
                price: $command->content->price,
                url: $command->content->url,
                imgUrl: $command->content->imgUrl,
            );

            $this->productRepository->save($product);
            $this->messageBus->dispatch(new ProductWasCreatedEvent($product->getId()));
        } catch (\Throwable $exception) {
            $this->logger->error('Product was not created: ', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'content' => serialize($command->content),
            ]);

            throw new ProductCreatingException();
        }
    }
}