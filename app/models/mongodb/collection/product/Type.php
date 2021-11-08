<?php

declare(strict_types=1);

namespace app\models\mongodb\collection\product;

use app\core\database\mongodb\MongoDb;

class Type extends MongoDb
{
    protected function collection(): string
    {
        return 'product_types';
    }

    public array $filter = [];
    public array $option = [];

    public function filter(): array
    {
        return $this->filter;
    }
    public function option(): array
    {
        return $this->option;
    }
}
