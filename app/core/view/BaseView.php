<?php
namespace app\core\view;

use app\core\App;
use app\core\exceptions\NotFoundException;

abstract class BaseView
{
    public string $contentType;
    public string $extention;
    public string $pathFile;

    private const STYLE = '.css';
    private const JS = '.js';
    private const HTML = '.html';


    abstract protected function viewAbs() : array;

    public function handleAb()
    {
      
    }
    

    function setFile()
    {
        $this->handleAb();
        $rootPath = App::$rootPath;
        $filePath =  $rootPath."/resources/$this->pathFile.$this->extention";
        if(file_exists($filePath)){
            header("Content-type: $this->contentType");    
            echo file_get_contents( $filePath);
        } else{
            throw new NotFoundException("khong tim thay duong dan");
        }

    }




}