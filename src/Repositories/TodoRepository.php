<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use PDO;

class TodoRepository extends BaseRepository
{
    private $table = 'todo';

    public function __construct(array $dbConfig)
    {
        parent::__construct($dbConfig);
    }

    /**
     * Fetch all todo items
     */
    public function getAll()
    {
        return $this->select($this->table, ['id', 'title', 'is_completed', 'completed_ts', 'sequence'], null, ['sequence']);
    }

    /**
     * Fetch all todo items that are not deleted
     * 
     * @return array
     * List of todo items that are not deleted. Each item is an array with title, completed and deleted info.
     */
    public function getActive(): array
    {
        return $this->select($this->table, ['id', 'title', 'is_completed', 'completed_ts', 'sequence'], ['is_deleted' => false], ['is_completed', 'completed_ts desc', 'sequence']);
    }

    /**
     * Get one todo item
     * 
     * @param string $id
     * ID of the todo item
     * 
     * @return array
     * Title, completed and deleted info of the todo item
     */
    public function getOne(string $id): array
    {
        return $this->select($this->table, ['id', 'is_completed', 'completed_ts', 'is_deleted', 'deleted_ts', 'sequence'], ['id' => $id]);
    }

    /**
     * Add one todo item
     * 
     * @param string $title
     * Title of the todo item
     * 
     * @return integer|null
     * ID of the newly added todo item if added successfull. Null otherwise
     */
    public function addOne(string $title)
    {
        return $this->insert($this->table, ['title' => $title]);
    }

    /**
     * Update one todo item
     * 
     * @param array $params
     * - title: New title of the todo item
     * - is_completed: Toggle if todo is completed
     * - sequence: Ordering of todo items
     * 
     * @return bool
     * True if todo item is updated successfully. False otherwise
     */
    public function updateOne(array $params, string $id)
    {
        $payload = [
            'title' => $params['title'],
            'is_completed' => $params['is_completed'] ? 1 : 0,
            'completed_ts' => $params['completed_ts']
        ];

        if (array_key_exists('sequence', $params)) {
            $payload['sequence'] = $params['sequence'];
        }

        return $this->update($this->table, $payload, ['id' => $id]);
    }

    /**
     * Mark one todo item as deleted
     * 
     * @param string $id
     * ID of the todo item
     * 
     * @return bool
     * True if todo item is deleted successfully. False otherwise
     */
    public function deleteOne(string $id)
    {
        return $this->softDelete($this->table, ['id' => $id]);
    }
}
