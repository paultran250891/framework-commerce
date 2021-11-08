<?php

namespace app\core\database\model;

use app\core\App;
use app\core\Database;
use app\core\Test;

abstract class ModelValidate
{
    public const RULE_RIQUIRED = "riquired";
    public const RULE_EMAIL = "email";
    public const RULE_MIN = "min";
    public const RULE_MAX = "max";
    public const RULE_PASS = "pass";
    public const RULE_MATCH = "match";
    public const RULE_UNIQUE = "unique";
    public const RULE_INT = 'int';
    public const RULE_NOTFOUND = 'notfound';
    private array $values = [];
    public array $errors = [];

    /* abstract */
    abstract public function rules(): array;
    abstract function matchTable(): array;
    /* end abstract */

    public function getNameDb()
    {
        return App::$app->database->name;
    }

    private function loadValue($data)
    {
        $rules = $this->rules();
        foreach (array_keys($data) as $attr) {
            $this->values[$attr] = trim($data[$attr]);
        }
        return $rules;
    }

    public function validate($data = [])
    {
        $rules = $this->loadValue($data);
        foreach ($rules as $attr => $rule) {
            foreach ($rule as $nameRule => $setRule) {
                switch ($nameRule) {
                    case self::RULE_RIQUIRED:
                        if (empty($this->values[$attr])) {
                            $this->addErrorForRule($attr, $nameRule, ['field' => $setRule['label']]);
                        }
                        break;
                    case self::RULE_INT:
                        if (!is_numeric($this->values[$attr])) {
                            $this->addErrorForRule($attr, $nameRule, ['field' => $setRule['label']]);
                        }
                        break;
                    case self::RULE_MAX:
                        if (strlen($this->values[$attr]) > $setRule['max']) {
                            $this->addErrorForRule($attr, $nameRule, ['max' => $setRule['max']]);
                        }
                        break;
                    case self::RULE_MATCH:
                        if (($this->values[$attr]) !== $this->values[$setRule['match']]) {
                            $this->addErrorForRule($attr, $nameRule, ['match' => $setRule['match']]);
                        }
                        break;
                    case self::RULE_NOTFOUND:
                        foreach ($this->matchTable() as $table) {
                            if ($table['value'] === $attr) {
                                $tableName = self::getNameDb() . "." . $table['table'];
                                $where = [$table['key'] => $this->values[$attr]];
                                $sql = "SELECT * FROM " . $tableName . ' WHERE ' . $this->strWhere($where);
                                if (!$this->statement($sql, $where)->fetch()) {
                                    $this->addErrorForRule($attr, $nameRule, ['field' => $setRule['label']]);
                                };
                            }
                        }
                        break;
                    case self::RULE_UNIQUE:
                        $tableName = self::getNameDb() . "." . $setRule['table'];
                        $where = [$attr => $this->values[$attr]];
                        $sql = "SELECT * FROM " . $tableName . ' WHERE ' . $this->strWhere($where);
                        $getValuerule = $this->statement($sql, $where)->fetch();
                        if ($getValuerule && $getValuerule[$setRule['key']] !=  $this->values[$setRule['keyAttr']]) {
                            $this->addErrorForRule($attr, $nameRule, ['field' => $setRule['label']]);
                        }
                        break;
                }
            }
        }
        if (empty($this->errors)) {
            foreach ($this->values as $key => $value) {
                $this->$key = $value;
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }

    private function addErrorForRule(string $attr, string $rule, $params = [])
    {
        $mess = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $k => $v) {
            $mess = str_replace("{{$k}}", $v, $mess);
        }
        $this->errors[$attr][] = $mess;
    }

    public function errorMessages()
    {
        return [
            self::RULE_INT => "{field} khong phai so ",
            self::RULE_RIQUIRED => "{field} không được để trống",
            self::RULE_EMAIL => "Email dinh dang khong dung",
            self::RULE_MAX => "nhap nhieu hon {max} ky tu",
            self::RULE_MIN => "nhap it hon {min} ky tu",
            self::RULE_MATCH => "khong trung voi {match}",
            self::RULE_UNIQUE => "{field} nay da ton tai ",
            self::RULE_NOTFOUND => "{field} nay khong ton tai",
        ];
    }

    public function statement($sql, $where = [])
    {
        $statement = App::$app->database->prepare($sql);
        if (!is_array($where)) {
            $statement->execute();
            return $statement;
        } else {
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
            return $statement;
        }
    }

    public function strWhere($where)
    {
        $strAttr = implode(' AND ', array_map(fn ($attr) => "$attr=:$attr", array_keys($where)));
        return  "  $strAttr ";
    }
}
