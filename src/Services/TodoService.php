<?php

namespace App\Services;

use App\Repositories\TodoRepository;
use App\Services\TextFormattingService;

class TodoService
{
    private $todoRepository;
    private $textformatter;

    public function __construct(TodoRepository $todoRepository, TextFormattingService $textFormattingService)
    {
        $this->todoRepository = $todoRepository;
        $this->textformatter = $textFormattingService;
    }

    public function getAllTodos()
    {
        return $this->todoRepository->getAll();
    }

    public function getActiveTodos()
    {
        return $this->todoRepository->getActive();
    }

    public function addOneTodo(array $params)
    {
        $params['title'] = $this->formatTitle($params['title']);
        $this->todoRepository->addOne($params);
    }

    public function updateOneTodo(array $params)
    {
        if (array_key_exists('id', $params) && !empty($params['id'])) {
            $id = $params['id'];
            unset($params['id']);

            $params['title'] = $this->formatTitle($params['title']);

            $this->todoRepository->updateOne($params, $id);
        }
    }

    public function deleteOneTodo(array $params)
    {
        if (array_key_exists('id', $params) && !empty($params['id'])) {
            $id = $params['id'];
            $this->todoRepository->deleteOne($id);
        }
    }

    private function formatTitle(string $title): string
    {
        $title = $this->textformatter->trim($title);
        $title = $this->textformatter->propercase($title);

        return $title;
    }
}
