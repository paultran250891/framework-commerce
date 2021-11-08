<?php

namespace app\core\database\model;

use app\core\App;
use app\core\Database;

abstract class Model
{
    public const RULE_RIQUIRED = "riquired";
    public const RULE_EMAIL = "email";
    public const RULE_MIN = "min";
    public const RULE_MAX = "max";
    public const RULE_PASS = "pass";
    public const RULE_MATCH = "match";
    public const RULE_UNIQUE = "unique";
    public const RULE_INT = 'int';
    public const RULE_ALREADY_EXITS = 'alreadyExits';


    public array $errors = [];

    public function getNameDb()
    {
        return App::$app->database->name;
    }

    public function loadData($data)
    {
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    abstract public function rules(): array;
    abstract public function labels(): array;

    public function validate()
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {

                /*    set cac ruleName */
                $ruleName = $rule;
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                }
                /* end */

                if ($ruleName === self::RULE_RIQUIRED && !$value) {
                    $this->addErrorForRule($attr, self::RULE_RIQUIRED);
                }

                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attr, self::RULE_EMAIL);
                }

                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attr, self::RULE_MIN, $rule);
                }

                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attr, self::RULE_MAX, $rule);
                }

                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {

                    $this->addErrorForRule($attr, self::RULE_MATCH, $rule);
                }

                if ($ruleName === self::RULE_INT && !is_numeric($value)) {

                    $this->addErrorForRule($attr, self::RULE_INT);
                }

                if ($ruleName === self::RULE_UNIQUE) {
                    $nameDb  = App::$app->database->name;
                    $className  = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attr;
                    $tableName = is_array($className::tableName()) ? $className::$table : $className::tableName();
                    $statement = App::$app->database->prepare("SELECT * FROM $nameDb.$tableName WHERE $uniqueAttr = :$uniqueAttr");
                    $statement->bindValue(":$uniqueAttr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attr, self::RULE_UNIQUE, ['field' => $this->labels()[$attr]]);
                    }
                }
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
            self::RULE_INT => "Day khong phai so ",
            self::RULE_RIQUIRED => "không được để trống",
            self::RULE_EMAIL => "Email dinh dang khong dung",
            self::RULE_MAX => "nhap nhieu hon {max} ky tu",
            self::RULE_MIN => "nhap it hon {min} ky tu",
            self::RULE_MATCH => "khong trung voi {match}",
            self::RULE_UNIQUE => "{field} nay da ton tai ",
        ];
    }

    public function hasError($attr)
    {

        return $this->errors[$attr] ?? false;
    }

    public function getFirstError($attr)
    {
        return $this->errors[$attr][0] ?? false;
    }
}
