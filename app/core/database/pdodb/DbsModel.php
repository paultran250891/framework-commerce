<?php

namespace app\core\pdodb\database;

use app\core\App;
use app\core\database\model\Model;

abstract class DbsModel extends Model
{
    public int $primaryKey;

    public function __construct()
    {
    }

    public function getNameDB()
    {
        return App::$app->database->name;
    }

    abstract public function tableName(): array;
    abstract public function attributes(): array;


    public function save(array $tableNames,  $match)
    {
        $tableDatas = $this->tableName();
        $id = 0;
        foreach ($tableNames as $name) {
            if ($tableDatas[$name][$match]) {
                $tableDatas[$name][$match] = $id;
            }
            $id = $this->insert($name,  $tableDatas[$name]);
        }
    }

    protected function insert($tableName,  array $data)
    {
        $tableName = $this->getNameDB() . '.' . $tableName;
        $attributes = array_keys($data);
        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $sql = "INSERT INTO $tableName (" . implode(',', $attributes) . ") VALUES (" . implode(',', $params) . ")";
        $statement = $this->prepare($sql);
        foreach ($attributes as $attr) {
            $statement->bindValue(":$attr", $data[$attr]);
        }
        return App::$app->database->getId($statement);
    }

    public function findOne($where)
    {
        $tableName = $this->getNameDB() . "." . static::$table;
        $attributes = array_keys($where);
        $sql = "SELECT * FROM $tableName WHERE " . implode(" AND ", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare($sql);
        foreach ($where as $attr => $value) {
            $statement->bindValue(":$attr", $value);
        }
        $statement->execute();
        return $statement->fetch();
    }

    public  function prepare($sql)
    {
        return App::$app->database->prepare($sql);
    }

    public function getWhere($table = false, $where = [], int $limit = null, int $offset = null)
    {
        $nameDb = $this->getNameDB();
        $limit = is_numeric($limit) ? $limit : 10;
        $offset = is_numeric($offset) ? $offset : 0;
        $tableName = $table ? $table : static::$table;
        $sql = !empty($where) ? "WHERE " . $where[0] . "= ?" : "";
        $sql = "SELECT * FROM $nameDb.$tableName $sql ORDER BY create_at ASC LIMIT $limit OFFSET $offset";
        $statement = self::prepare($sql);
        if (!empty($where[1])) {
            $statement->execute([$where[1]]);
        } else {
            $statement->execute([]);
        }
        return $statement->fetchAll() ?? false;
    }

    public function update($where,  $attrs)
    {
        $table = $this->getNameDB() . '.' . static::$table;
        $whereAttr =  array_map(fn ($attr) =>  "$attr=:$attr", array_keys($where));
        $value = implode(' , ', array_map(fn ($attr) => "$attr=:$attr",  $attrs));
        $sql = "UPDATE $table SET $value  WHERE " . implode(' AND ', $whereAttr);
        $statement = self::prepare($sql);
        foreach ($attrs as $attr) {
            $statement->bindValue(":$attr", $this->$attr);
        }

        foreach ($where as $key => $value) {

            $statement->bindValue(":$key", $value);
        }
        $statement->execute();
    }

    public function delete($where)
    {
        $tableName = $this->getNameDB() . "." . static::$table;
        $attributes =  array_map(fn ($attr) =>  "$attr=:$attr", array_keys($where));


        $sql = "DELETE FROM $tableName  WHERE " . implode(' AND ', $attributes);
        $statement = self::prepare($sql);
        foreach ($where as $key => $value) {

            $statement->bindValue(":$key", $value);
        }
        $statement->execute();
    }
}
