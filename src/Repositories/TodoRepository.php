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

    public function getAll()
    {
        $sql = "
            SELECT id, title, is_completed, create_ts, completed_ts 
            FROM $this->table
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive()
    {
        $sql = "
            SELECT id, title, is_completed, create_ts, completed_ts 
            FROM $this->table
            WHERE is_deleted IS FALSE
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addOne(array $params)
    {
        $this->insert($this->table, $params);
    }

    public function updateOne(array $params, string $id)
    {
        $this->update($this->table, $params, ['id' => $id]);
    }

    public function deleteOne(string $id)
    {
        $this->softDelete($this->table, ['id' => $id]);
    }
}
