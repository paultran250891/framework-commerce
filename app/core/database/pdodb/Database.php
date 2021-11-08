<?php

namespace app\core\database\pdodb;

class Database
{
    public \PDO $pdo;

    public function __construct(array $configDb)
    {
        $dsn = $configDb['dsn'] ?? '';
        $pass = $configDb['pass'] ?? '';
        $user = $configDb['user'] ?? '';
        $this->name = $configDb['name'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function prepare($sql)
    {

        return $this->pdo->prepare($sql);
    }

    public function fetch($sql)
    {

        $stament = $this->prepare($sql);
        $stament->execute();
        return $stament->fetch();
    }

    public function getId($attr = null)
    {
        return $this->pdo->lastInsertId($attr);
    }

    public function search($table, $where = [])
    {
        $attributes = array_keys($where);
        $sql = "SELECT * FROM $table WHERE " . implode("AND", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = $this->pdo->prepare($sql);
        foreach ($where as $attr => $value) {
            $statement->bindValue(":$attr", $value);
        }
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_OBJ);
    }
}
