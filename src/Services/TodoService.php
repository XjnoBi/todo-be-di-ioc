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

    /**
     * Add one todo item.
     * Trims and formats the title to propercase
     * 
     * @param array $params
     * - $title: New title of the todo item
     * 
     * @return integer|null
     * ID of the newly added todo item if added successfull. Null otherwise
     */
    public function addOneTodo(array $params)
    {
        $title = $this->formatText($params['title']);
        $id = $this->todoRepository->addOne($title);
        if ($id) {
            // bump up sequence of existing todo items
            $activeTodos = $this->getActiveTodos();
            foreach ($activeTodos as $todo) {
                if ($todo['id'] == $id) {
                    continue;
                }

                $todo['sequence'] = $todo['sequence'] + 1;
                $this->todoRepository->updateOne($todo, $todo['id']);
            }

            return $id;
        }

        return false;
    }

    /**
     * Update one todo item.
     * Trims and formats the title to propercase.
     * Re-sorts the todo items if sequence has changed
     * 
     * @param array $params
     * - title: New title of the todo item
     * - is_completed: Toggle if todo is completed
     * - sequence: Order where todo item is placed
     * 
     * @return bool
     * True if todo item is updated successfully. False otherwise
     */
    public function updateOneTodo(array $params)
    {
        if (array_key_exists('id', $params) && !empty($params['id'])) {
            $id = $params['id'];
            unset($params['id']);

            $params['title'] = $this->formatText($params['title']);
            $oldTodo = $this->todoRepository->getOne($id)[0];

            $success = $this->todoRepository->updateOne($params, $id);
            if (!$success) {
                return false;
            }

            if (array_key_exists('sequence', $params) && is_int($params['sequence'])) {
                if ($oldTodo['sequence'] == $params['sequence']) {
                    return true;
                }

                $isMovingUp = $oldTodo['sequence'] > $params['sequence'];

                $activeTodos = $this->getActiveTodos();
                foreach ($activeTodos as $todo) {
                    if ($todo['id'] == $id) {
                        continue;
                    }

                    if ($isMovingUp) {
                        // skip if higher in order
                        if ($todo['sequence'] < $params['sequence']) {
                            continue;
                        }

                        $todo['sequence'] = $todo['sequence'] + 1;
                        $this->todoRepository->updateOne($todo, $todo['id']);
                    }

                    if (!$isMovingUp) {
                        // skip if lower in order
                        if ($todo['sequence'] > $params['sequence']) {
                            continue;
                        }

                        $todo['sequence'] = $todo['sequence'] - 1;
                        $this->todoRepository->updateOne($todo, $todo['id']);
                    }
                }
            }

            return true;
        }

        return false;
    }

    public function deleteOneTodo(array $params)
    {
        if (array_key_exists('id', $params) && !empty($params['id'])) {
            $id = $params['id'];
            return $this->todoRepository->deleteOne($id);
        }
    }

    /**
     * Format the title.
     * Remove leading and trailing whitespace.
     * Change to propercase
     * 
     * @param string $text
     */
    private function formatText(string $text): string
    {
        $text = $this->textformatter->trim($text);
        $text = $this->textformatter->propercase($text);

        return $text;
    }
}
