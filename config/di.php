<?php

use DI\ContainerBuilder;
use App\Controllers\TodoController;
use App\Repositories\TodoRepository;
use App\Services\TextFormattingService;
use App\Services\TodoService;

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

$dbConfig = require __DIR__ . '/db.php';

$containerBuilder->addDefinitions([
    // Todo module
    TodoService::class => \DI\autowire('App\Services\TodoService'),
    TodoController::class => \DI\autowire('App\Controllers\TodoController'),
    TodoRepository::class => \DI\autowire('App\Repositories\TodoRepository')->constructorParameter('dbConfig', $dbConfig),
    // Utility
    TextFormattingService::class => \DI\autowire('App\Services\TextFormattingService'),
]);

return $containerBuilder->build();
