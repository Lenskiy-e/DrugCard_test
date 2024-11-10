<?php

declare(strict_types=1);

namespace App\UI\CLI;

use App\Application\Content\ContentDTO;
use App\Application\Content\Exception\ContentLoadException;
use App\Application\Content\Loader\LoaderType;
use App\Application\Content\Query\GetContentQuery;
use App\Application\Product\Command\Create\CreateCommand;
use App\Application\Product\Exception\ProductCreatingException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(name: 'app:run', description: 'Parse pages and populate storages')]
class RunParsingCommand extends Command
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // mock pages
        $pages = [
            'https://allo.ua/ua/products/mobile/',
            'https://allo.ua/ua/products/notebooks/',
            'https://allo.ua/ua/products/kombajny/',
        ];
        $result = Command::SUCCESS;

        foreach ($pages as $page) {
            try {
                $query = $this->messageBus->dispatch(new GetContentQuery($page, LoaderType::ALLO));
                /** @var ContentDTO[] $content */
                $content = $query->last(HandledStamp::class)?->getResult();

                foreach ($content as $item) {
                    $this->messageBus->dispatch(new CreateCommand($item));
                }
            } catch (ContentLoadException|ProductCreatingException) {
                continue;
            } catch (\Throwable $e) {
                $this->logger->error(
                    'Error during handling command',
                    [
                        'command' => 'app:run',
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]
                );
                $result = Command::FAILURE;
            }
        }

        return $result;
    }
}
