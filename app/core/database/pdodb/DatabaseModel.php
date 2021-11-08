<?php

namespace app\core\pdodb\database;

use app\core\App;
use app\core\database\model\ModelValidate;

abstract class DatabaseModel extends ModelValidate
{
    abstract function tableName(): string;
    abstract function matchTable(): array;
    abstract function attributes(): array;

    public function getNameDb($table = null)
    {
        $table = $table ?? $this->tableName();
        return parent::getNameDb() . '.' . $table;
    }

    public function getValue($data)
    {
        if (!$data) {
            return [];
        }
        foreach ($this->matchTable() as $match) {
            $where = [$match['key'] => $data[$match['value']]];
            $fetch = $this->fetch($where, $match['table']);
            if (is_array($match['get'])) {
                $set[$match['get'][1]] =  $fetch[$match['get'][0]];
            } else {
                $set[$match['get']] =  $fetch[$match['get']];
            }
        }
        return $set ?? [];
    }

    public function fetch($where, $table)
    {
        $sql = ($where !== 'all') ? 'SELECT * FROM ' . $this->getNameDb($table) . ' WHERE ' .  $this->strWhere($where)
            : 'SELECT * FROM ' . $this->getNameDb($table);
        return $this->statement($sql, $where)->fetch();
    }



    public function find($where, $orderby = 'id', $limit = "all", $offset = 0)
    {
        $setSql = " ORDER BY  $orderby ASC LIMIT $limit OFFSET $offset ";
        if ($where === 'all') {
            $sql = 'SELECT * FROM ' . $this->getNameDb() . $setSql;
        } else {
            $sql = 'SELECT * FROM ' . $this->getNameDb() . ' WHERE ' . $this->strWhere($where) . $setSql;
        }
        foreach ($this->statement($sql, $where)->fetchAll() as $data) {
            $data =  array_replace($data, $this->getValue($data));
            $datas[] = $data;
        }
        return $datas;
    }

    public function findOne($where)
    {
        $sql = 'SELECT * FROM ' . $this->getNameDb() . ' WHERE ' . $this->strWhere($where);
        $data = $this->statement($sql, $where)->fetch();
        return $data ? array_replace($data, $this->getValue($data)) : false;
    }

    public function searchOne($where)
    {
        $sql = 'SELECT * FROM ' . $this->getNameDb() . ' WHERE ' . $this->strWhere($where);
        $data = $this->statement($sql, $where)->fetch();

        foreach ($this->matchTable() as $table => $attr) {
            $whereMatch[$attr['key']] = $data[$attr['value']];
            $sql = 'SELECT * FROM ' . $this->getNameDb($table) . ' WHERE ' . $this->strWhere($whereMatch);
            $dataMatch[$attr['detail']] = $this->statement($sql, $whereMatch)->fetchAll();
            $data = array_replace($data, $dataMatch);
        }
        return  $data;
    }

    public function insert($table, $where)
    {
        $attrs = implode(',', array_keys($where));
        $params = implode(',',  array_map(fn ($param) => ":$param", array_keys($where)));
        $sql = "INSERT INTO $table  ($attrs)  VALUES ($params) ";
        return $this->statement($sql, $where);
    }

    public function save()
    {
        foreach ($this->matchTable() as $table) {
            $tableName = $this->getNameDb($table['table']);
            $whereTable = $table['attr'] ?? false;
            if ($whereTable) {
                $this->insert($tableName, $whereTable);
                $this->{$table['value']} = App::$app->database->getId();
            }
        }
        foreach ($this->attributes() as $attr) {
            $where[$attr] = $this->{$attr};
        };
        $this->insert($this->getNameDb(), $where);
    }

    public function update($where = [], $set = [], $table = null)
    {
        $value = implode(' , ', array_map(fn ($attr) => "$attr=:$attr",  $set));
        $sql = "UPDATE " . $this->getNameDb($table) . ' SET ' . $value . ' WHERE ' . $this->strWhere($where);
        foreach ($set as $attr) {
            $data[$attr] = $this->{$attr};
        }
        $data =  $where + $data;
        $this->statement($sql, $data);
    }

    public function delete($where, $table = null)
    {
        $sql = 'DELETE FROM ' . $this->getNameDb($table) . ' WHERE ' . $this->strWhere($where);
        $this->statement($sql, $where);
    }

    public function test($where, $table)
    {
        $sql = ($where !== 'all') ? 'SELECT * FROM ' . $this->getNameDb($table) . ' WHERE ' .  $this->strWhere($where)
            : 'SELECT * FROM ' . $this->getNameDb($table);

        return $this->statement($sql, $where)->fetchAll();
    }

    public function filter($params)
    {
        $where = $params['where'] ?? '';
        $limit = $params['limit'] ?? '';
        $offset = $params['offset'] ?? '';
        $orderby = $params['orderby'] ?? '';
        $setSql = " ORDER BY  $orderby ASC LIMIT $limit OFFSET $offset ";
        if ($where === 'all') {
            $sql = 'SELECT * FROM ' . $this->getNameDb() . $setSql;
        } else {
            $sql = 'SELECT * FROM ' . $this->getNameDb() . ' WHERE ' . $this->strWhere($where) . $setSql;
        }

        foreach ($this->statement($sql, $where)->fetchAll() as $data) {
            $data =  array_replace($data, $this->getValue($data));
            $datas[] = $data;
        }
        return $datas;
    }
}
