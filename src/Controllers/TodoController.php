<?php

namespace App\Controllers;

use App\Services\TodoService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TodoController
{
    private $todoService;

    public function __construct(TodoService $todoService)
    {
        $this->todoService = $todoService;
    }

    public function index(Request $request, Response $response): Response
    {
        $todos = $this->todoService->getActiveTodos();
        $response->getBody()->write(json_encode($todos));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function add(Request $request, Response $response): Response
    {
        $todo = $this->todoService->addOneTodo($request->getParsedBody());
        $response->getBody()->write(json_encode($todo));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response): Response
    {
        $todo = $this->todoService->updateOneTodo($request->getParsedBody());
        $response->getBody()->write(json_encode($todo));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response): Response
    {
        $todo = $this->todoService->deleteOneTodo($request->getParsedBody());
        $response->getBody()->write(json_encode($todo));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
