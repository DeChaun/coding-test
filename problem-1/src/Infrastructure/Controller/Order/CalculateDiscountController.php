<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Order;

use App\Application\Command\OrderFactory;
use Psr\Http\Message\ResponseInterface;

final readonly class CalculateDiscountController
{
    public function __construct(
        private OrderFactory $orderFactory,
    ) {
    }

    public function __invoke($request, $response, $args): ResponseInterface
    {
        $body = $request->getParsedBody();
        $order = $this->orderFactory->fromApiData($body);

        $response->getBody()->write(json_encode($order->toArray()));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
