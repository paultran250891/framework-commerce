<?php

namespace app\core\pdodb\database;

use app\core\App;
use app\core\database\model\Model;

abstract class DbModel extends Model
{
    public int $primaryKey;
    public array $tableMatch = [];

    public function __construct()
    {
    }

    public function getNameDB()
    {
        return App::$app->database->name;
    }

    abstract public function attributes(): array;
    abstract public function tableName(): string;
    abstract public function primaryKey(): string;

    public function save()
    {
        $nameDb = App::$app->database->name;
        $tableName = $this->tableName();
        $attributes = $this->attributes();

        $params = array_map(fn ($attr) => ":$attr", $attributes);

        $sql = "INSERT INTO $nameDb.$tableName (" . implode(',', $attributes) . ") VALUES (" . implode(',', $params) . ")";

        $statement = $this->prepare($sql);

        foreach ($attributes as $attr) {

            $statement->bindValue(":$attr", $this->{$attr});
        }

        return $statement->execute();
        // return App::$app->database->getId($statement);


    }

    public function update($where,  $attrs = null)
    {
        $attr = $attr ?? $this->attributes();
        $table = $this->getNameDB() . '.' . $this->tableName();
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

    public function findOne($where)
    {
        $dataName =  App::$app->database->name;
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = "SELECT * FROM $dataName.$tableName WHERE " . implode(" AND ", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare($sql);
        foreach ($where as $attr => $value) {
            $statement->bindValue(":$attr", $value, \PDO::PARAM_STR);
        }
        $statement->execute();
        return $statement->fetch();
    }

    public  function prepare($sql)
    {

        return App::$app->database->prepare($sql);
    }



    public function getAll()
    {
        $tableName = $this->getNameDB() . "." . static::tableName();
        $sql = "SELECT * FROM $tableName";
        $statement = self::prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function fetchOne()
    {
        $tableName = static::tableName();
        $id = $this->primaryKey();

        $sql = "SELECT * FROM $tableName ORDER BY $id DESC LIMIT 1";
        $statement = self::prepare($sql);
        $statement->execute();
        return $statement->fetch();
    }

    public function delete($where)
    {
        $tableName = $this->getNameDB() . "." . $this->tableName();
        $attributes =  array_map(fn ($attr) =>  "$attr=:$attr", array_keys($where));
        $sql = "DELETE FROM $tableName  WHERE " . implode(' AND ', $attributes);
        $statement = self::prepare($sql);
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        $statement->execute();
    }
}
