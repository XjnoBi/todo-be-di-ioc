<?php

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token");

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\TodoController;
use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../config/di.php';
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();

// routes
$app->get('/todo', [TodoController::class, 'index']);
$app->post('/todo', [TodoController::class, 'add']);
$app->put('/todo', [TodoController::class, 'update']);
$app->delete('/todo', [TodoController::class, 'delete']);

$app->run();
