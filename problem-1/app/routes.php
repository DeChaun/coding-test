<?php

declare(strict_types=1);

use App\Infrastructure\Controller\Order\CalculateDiscountController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/order', function (Group $group) use ($app) {
        $group->post('/calculate-discount', CalculateDiscountController::class . ':__invoke');
    });
};
