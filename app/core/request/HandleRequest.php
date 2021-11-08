<?php

namespace app\core\request;

use app\core\exceptions\NotFoundException;
use app\core\Test;

abstract class HandleRequest
{
    public string $path;

    abstract public function path(): string;

    public function __construct()
    {

        $this->handlePath();
    }

    public function handlePath()
    {


        $this->path = preg_replace('/[^A-Za-z0-9\-\.\/\?\=\&\_\@\%\+\$]/', '', $this->path());
        if ($this->path === $this->path()) {
            $position = strpos($this->path, '?');
            if ($position !== false) {
                $this->path =  substr($this->path, 0, $position);
            }
        } else {
            throw new NotFoundException('duong dan khong ton tai');
        }
    }

    public function getPath()
    {
        return  $this->handledPath ?? '/';
    }

    public function get()
    {
        return $_GET;
    }
}
