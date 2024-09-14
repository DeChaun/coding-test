<?php

declare(strict_types=1);

use App\Application\Command\Product\GetCustomer;
use App\Application\Command\Product\GetCustomerHandler;
use App\Application\Command\Product\GetProduct;
use App\Application\Command\Product\GetProductHandler;
use App\Application\Settings\SettingsInterface;
use App\Domain\Repository\CustomerRepository as CustomerRepositoryInterface;
use App\Domain\Repository\ProductRepository as ProductRepositoryInterface;
use App\Infrastructure\Repository\FileCustomerRepository;
use App\Infrastructure\Repository\FileProductRepository;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        MessageBusInterface::class => function (ContainerInterface $c) {
            $containerHandlerLocator = new HandlersLocator([
                GetProduct::class => [ fn (GetProduct $command) => $c->get(GetProductHandler::class)($command) ],
                GetCustomer::class => [ fn (GetCustomer $command) => $c->get(GetCustomerHandler::class)($command) ],
            ]);

            $middlewares = [
                new HandleMessageMiddleware($containerHandlerLocator),
            ];

            return new MessageBus($middlewares);
        },
        CustomerRepositoryInterface::class => function (ContainerInterface $c) {
            return new FileCustomerRepository();
        },
        ProductRepositoryInterface::class => function (ContainerInterface $c) {
            return new FileProductRepository();
        },
    ]);
};
