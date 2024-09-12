<?php

declare(strict_types=1);

use App\Application\Command\CreateOrderFromRequestDataCommand;
use App\Application\Command\CreateOrderFromRequestDataCommandHandler;
use App\Application\Command\CreateOrderItemFromRequestDataCommand;
use App\Application\Command\CreateOrderItemFromRequestDataCommandHandler;
use App\Application\Settings\SettingsInterface;
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
                CreateOrderFromRequestDataCommand::class => [
                    fn (CreateOrderFromRequestDataCommand $command) =>
                        $c->get(CreateOrderFromRequestDataCommandHandler::class)($command)
                ],
                CreateOrderItemFromRequestDataCommand::class => [
                    fn (CreateOrderItemFromRequestDataCommand $command) =>
                        $c->get(CreateOrderItemFromRequestDataCommandHandler::class)($command)
                ],
            ]);

            $middlewares = [
                new HandleMessageMiddleware($containerHandlerLocator),
            ];

            return new MessageBus($middlewares);
        },
    ]);
};
