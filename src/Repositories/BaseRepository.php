<?php

namespace App\Repositories;

use PDO;

class BaseRepository
{
    private $dbConfig;
    protected $db;

    public function __construct(array $dbConfig)
    {
        $this->dbConfig = $dbConfig['database'];
        $this->initializeDb();
    }

    private function initializeDb()
    {
        try {
            $this->db = new PDO(
                "mysql:host=" . $this->dbConfig['host'] . ";dbname=" . $this->dbConfig['database'] . ";charset=" . $this->dbConfig['charset'],
                $this->dbConfig['username'],
                $this->dbConfig['password']
            );
        } catch (\PDOException $e) {
            die($e);
            throw new \RuntimeException('Failed to connect to database');
            exit(1);
        }
    }

    protected function select(string $table, array $fields, ?array $whereFields = [], array $orderBy = [], $limit = null)
    {
        try {
            $sql = "SELECT " . implode(', ', $fields) . " FROM $table";

            if (!empty($whereFields)) {
                $where = [];
                foreach ($whereFields as $field => $value) {
                    $where[] = "$field = :w$field";
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            }

            if (!empty($orderBy)) {
                $sql .= " ORDER BY " . implode(', ', $orderBy);
            }

            if ($limit !== null) {
                $sql .= " LIMIT $limit";
            }

            $statement = $this->db->prepare($sql);

            foreach ($whereFields as $field => $value) {
                $statement->bindValue(":w$field", $value);
            }

            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    protected function insert(string $table, array $fields)
    {
        try {
            $sql = "INSERT INTO $table (" . implode(',', array_keys($fields)) . ") VALUES (:" . implode(', :', array_keys($fields)) . ")";
            $statement = $this->db->prepare($sql);

            foreach ($fields as $field => $value) {
                $statement->bindValue(":$field", $value);
            }

            $statement->execute();
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    protected function update(string $table, array $fields, $whereFields)
    {
        try {
            $sql = "UPDATE $table SET ";

            $set = [];
            foreach ($fields as $field => $value) {
                $set[] = "$field = :s$field";
            }

            $where = [];
            foreach ($whereFields as $field => $value) {
                $where[] = "$field = :w$field";
            }

            $statement = $this->db->prepare($sql . implode(', ', $set) . " WHERE " . implode(' AND ', $where));
            foreach ($fields as $field => $value) {
                $statement->bindValue(":s$field", $value);
            }

            foreach ($whereFields as $field => $value) {
                $statement->bindValue(":w$field", $value);
            }

            $statement->execute();
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    protected function delete(string $table, $whereFields)
    {
        try {
            $sql = "DELETE FROM $table WHERE ";

            $where = [];
            foreach ($whereFields as $field => $value) {
                $where[] = "$field = :w$field";
            }

            $statement = $this->db->prepare($sql . implode(' AND ', $where));
            foreach ($whereFields as $field => $value) {
                $statement->bindValue(":w$field", $value);
            }

            $statement->execute();
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    protected function softDelete(string $table, $whereFields)
    {
        $fields = [
            'is_deleted' => true,
            'deleted_ts' => date('Y-m-d H:i:s')
        ];
        return $this->update($table, $fields, $whereFields);
    }
}
