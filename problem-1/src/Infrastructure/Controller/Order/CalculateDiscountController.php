<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Order;

use App\Application\Command\Discount\ComputeDiscount;
use App\Infrastructure\Factory\OrderFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class CalculateDiscountController
{
    use HandleTrait;

    public function __construct(
        private readonly OrderFactory $orderFactory,
        MessageBusInterface $queryBus,
    ) {
        $this->messageBus = $queryBus;
    }

    /**
     * @param array<int, mixed> $args
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        assert(is_array($body));

        $order = $this->orderFactory->fromApiData($body);

        $discountOrder = $this->handle(new ComputeDiscount($order));

        $response->getBody()->write('test response');
        return $response->withHeader('Content-Type', 'application/json');
    }
}
