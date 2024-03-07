<?php

namespace App\Services;

use App\Repositories\TodoRepository;

class TodoService
{
    private $todoRepository;

    public function __construct(TodoRepository $todoRepository)
    {
        $this->todoRepository = $todoRepository;
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
        $this->todoRepository->addOne($params);
    }

    public function updateOneTodo(array $params)
    {
        if (array_key_exists('id', $params) && !empty($params['id'])) {
            $id = $params['id'];
            unset($params['id']);

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
}
