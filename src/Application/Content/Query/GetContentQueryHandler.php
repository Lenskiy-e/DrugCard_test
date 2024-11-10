<?php

declare(strict_types=1);

namespace App\Application\Content\Query;

use App\Application\Content\Exception\ContentLoadException;
use App\Application\Content\Loader\LoaderFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
final readonly class GetContentQueryHandler
{
    public function __construct(
        private LoaderFactoryInterface $loaderFactory,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws ContentLoadException
     */
    public function __invoke(GetContentQuery $query): array
    {
        try {
            return $this->loaderFactory->get($query->type)->load($query->url);
        } catch (Throwable $exception) {
            $this->logger->error('Error handling GetContentQuery', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'url' => $query->url,
                'loader' => $query->type->value,
            ]);

            throw new ContentLoadException();
        }
    }
}