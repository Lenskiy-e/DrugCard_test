<?php

declare(strict_types=1);

namespace App\UI\Web;

use App\Application\Product\Query\GetCollection\GetCollectionQuery;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[
    AsController,
    Route(path: '/api/list', name: 'api_list', methods: 'GET')
]
class GetListAction extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private MessageBusInterface $messageBus,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $status = JsonResponse::HTTP_OK;

        try {
            $query = new GetCollectionQuery(
                (int)$request->get('page', 1),
                (int)$request->get('limit', 10)
            );

            $data = $this->messageBus->dispatch($query)->last(HandledStamp::class)?->getResult();
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();

            $data = ['error' => $previous?->getMessage()];
            $status = JsonResponse::HTTP_BAD_REQUEST;
        } catch (\Throwable $e) {
            $msg = 'Error retrieving collection';

            $this->logger->error($msg, [
                'message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);

            $data = ['error' => $msg];
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->json($data, $status);
    }
}