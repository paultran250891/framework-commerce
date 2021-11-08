<?php

namespace app\core\view;

use app\core\App;
use app\core\exceptions\ForbiddenException;
use app\core\exceptions\NotFoundException;
use app\core\lib\Test;
use app\core\request\Request;
use app\core\response\Response;

class SetFileView extends BaseView
{
    private $file = false;
    private $header = false;
    private $content = false;
    public $cacheTime = 200;
    // public $cacheFile;
    protected static string $css = 'text/css';
    protected static string $js = 'application/javascript';
    protected static string $map = 'txt';
    protected static string $jpg = ' image/jpeg';
    protected static string $png = ' image/jpeg';
    protected static string $txt = 'txt';
    protected static string $ttf = 'application/x-font-ttf';
    protected static string $woff = 'application/x-font-woff';
    protected static string $woff2 = 'application/font-woff2';

    public function ViewAbs(): array
    {
        return [];
    }

    protected string $filePath;

    public function __construct(Request $request)
    {
        $this->path = $request->path;

        $n = strpos($this->path, '/', 1) + 1;
        $this->filePath = substr($this->path, $n, strlen($this->path) - $n);
    }

    public function style()
    {
        $this->file =  App::$rootPath . "/resources/style/css/$this->filePath";
    }

    public function script(Request $request, Response $response)
    {
        $this->file =  App::$rootPath . "/resources/js/$this->filePath";
    }

    public function image(Request $request, Response $response)
    {

        $this->file =  App::$rootPath . "/resources/img/$this->filePath";
    }

    public function lib()
    {
        $this->file =  App::$rootPath . "/resources/lib/$this->filePath";
    }

    public function setFile()
    {
        $file = pathinfo($this->file);
        // Test::show($file);
        $this->header = self::${$file['extension']} ?? false;
        
    }

    public function __destruct()
    {
         
        $this->setFile();
        
        if($this->header == ' image/jpeg'){
            header("Cache-Control: max-age=2592000");
          
            header("Expires: Thu, 19 Nov 2022 08:52:00 GMT");
        }
        
        header('Content-type:' . $this->header);
        echo file_get_contents($this->file);
        if(!file_exists($this->file)){
            throw new NotFoundException("Test co loi lien he minh nhe!!! thank ban da ghe tham");
        }
        
    }
}
