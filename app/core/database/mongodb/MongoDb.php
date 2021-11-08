<?php

namespace app\core\database\mongodb;

abstract class MongoDb extends DatabaseMongodb
{
    abstract protected function collection(): string;
    abstract protected function filter(): array;
    abstract protected function option(): array;

    public  function __construct()
    {
    }
    public  function find()
    {
        $option = ['projection' => $this->option()];
        $result = $this->connectDb()->{$this->collection()}->find($this->filter(), $option);
        return json_decode(json_encode($result->toArray(), true), true);
    }

    public function findOne()
    {
        $option = ['projection' => $this->option()];
        $result = $this->connectDb()->{$this->collection()}->findOne($this->filter(), $option);
        return json_decode(json_encode($result, true), true);
    }

    public function insert($data)
    {
        $result = $this->connectDb()->{$this->collection()}->insertOne($data);
        return $result->getInsertedId();
    }

    public function updateOne($filter, $set)
    {
        $result = $this->connectDb()->{$this->collection()}->updateOne($filter, $set);
        // return $result->getInsertedId();
        return true;
    }

    public function count()
    {
        return $this->connectDb()->{$this->collection()}->count($this->filter());
        
    }

    public function aggregate()
    {
        
        $result = $this->connectDb()->{$this->collection()}->aggregate($this->filter());
        return json_decode(json_encode($result->toArray(), true), true);
    }

    public function delete()
    {
        if($this->connectDb()->{$this->collection()}->deleteOne($this->filter())){
            return true;
        };
    }
}
