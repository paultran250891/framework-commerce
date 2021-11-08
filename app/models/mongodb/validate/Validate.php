<?php

namespace app\models\mongodb\validate;

use app\core\database\mongodb\MongoDb1;
use app\core\exceptions\NotFoundException;
use app\core\lib\Test;

abstract class Validate
{
    protected const RULE_RIQUIRED = "riquired";
    protected const RULE_EMAIL = "email";
    protected const RULE_MIN = "min";
    protected const RULE_MAX = "max";
    protected const RULE_PASS = "pass";
    protected const RULE_MATCHDB = "matchdb";
    protected const RULE_MATCH = "match";
    protected const RULE_UNIQUE = "unique";
    protected const RULE_INT = 'int';
    protected const RULE_ALREADY_EXITS = 'alreadyExits';

    public array $errors = [];

    abstract public function rules(): array;

    public function _validate(): bool
    {
        foreach ($this->rules() as $attr => $rules) {
            foreach ($rules as $rule => $value) {

                switch ($rule) {
                    case self::RULE_RIQUIRED:
                        if (empty($this->$attr)) {

                            $this->addErrorForRule($attr, $rule, $value[0] ?? null);
                        }
                        break;
                    case self::RULE_INT:
                        if (!is_numeric($this->$attr)) {
                            $this->addErrorForRule($attr, $rule, $value[0] ?? null);
                        }
                        break;
                    case self::RULE_EMAIL:
                        if (!filter_var($this->$attr, FILTER_VALIDATE_EMAIL)) {
                            $this->addErrorForRule($attr, $rule);
                        }
                        break;
                    case self::RULE_MATCHDB:
                        $collection = new $value[0]();
                        $collection->filter = [$attr => $this->$attr];
                        if ($collection->count() < 1) {
                            $this->addErrorForRule($attr, $rule, $value[1]);
                        }
                        break;
                    case self::RULE_MATCH:
                        if ($this->$attr !== $this->{$value[0]}) {
                            $this->addErrorForRule($attr, $rule, $value[1]);
                        }
                        break;
                    case self::RULE_UNIQUE:
                        $collection = new $value[0];
                        $collection->filter = [$attr => $this->$attr];
                        if ($collection->count() > 0) {
                            $this->addErrorForRule($attr, $rule, $value[1]);
                        }
                        break;
                    case self::RULE_MAX:
                        if (strlen($this->$attr) > $value[0]) {
                            $this->addErrorForRule($attr, $rule, $value[1], $value[0]);
                        }
                        break;
                    case self::RULE_MIN:
                        if (strlen($this->$attr) < $value[0]) {
                            $this->addErrorForRule($attr, $rule, $value[1], $value[0]);
                        }
                        break;
                    default:
                        throw new NotFoundException('truyen tham so rules khong dung');
                        break;
                }
            }
        }
        return empty($this->errors) ? true : false;
    }

    public function loadData(array $data = [])
    {
        foreach ($data as $attr => $value) {
            $this->{$attr} = $value;
        }
    }

    private function addErrorForRule($attr, string $rule, string $lable = null, $value = null)
    {
        $mess = $this->errorMessages()[$rule] ?? '';
        $mess = str_replace('{label}', $lable ?? ucwords($attr), $mess);
        $mess = str_replace('{value}', $value, $mess);

        $this->addError($attr, $mess);
    }

    public function errorMessages()
    {
        return [
            self::RULE_INT => "dữ liệu không phải số {label}",
            self::RULE_RIQUIRED => "{label} không được để trống",
            self::RULE_EMAIL => "{label} định dạng không đúng",
            self::RULE_MAX => "{label} nhập nhiều hơn {value} ký tự",
            self::RULE_MIN => "{label} nhập ít hơn {value} ký tự",
            self::RULE_MATCHDB => "{label} không tồn tại",
            self::RULE_MATCH => "không trùng với {label}",
            self::RULE_UNIQUE => "{label} này đã tồn tại",
        ];
    }

    public function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }
}
