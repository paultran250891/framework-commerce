<?php

namespace app\core\view;

use app\core\App;
use app\core\exceptions\NotFoundException;
use app\core\view\BaseView;
use app\core\view\SetFileView;

class View extends SetFileView
{

    public string $layout = 'index.html';
    public string $title = 'tủ kệ mỹ phẩm';
    protected string $fileLayout;
    protected array $content = [];

    public function __construct()
    {
    }

    public function rootPath(): string
    {

        return App::$rootPath;
    }


    public function file($file)
    {
        $file = $this->rootPath() . "/views/$file";
        if (file_exists($file)) {

            ob_start();
            include $file;
            return ob_get_clean();
        } else {
            throw new NotFoundException("error content khong tim thay trong layout");
        }
    }

    public function renderLayout()
    {

        $file = $this->rootPath() . "/views/layouts/$this->layout";
        if (file_exists($file)) {
            ob_start();
            include $file;
            $this->fileLayout = ob_get_clean();
            $contents = explode("{{", $this->fileLayout);
            foreach ($contents as $content) {
                if (stripos($content, "}}")) {
                    $number  = strpos($content, "}}");
                    $content = substr($content, 0, $number);
                    if (file_exists($this->rootPath() . "/views/$content")) {
                        ob_start();
                        include $this->rootPath() . "/views/$content";
                        $this->content["{{{$content}}}"] = ob_get_clean();
                    } elseif ($content === 'title') {
                        $this->content["{{{$content}}}"] = $this->title;
                    } elseif ($content === 'content') {
                        $this->content["{{{$content}}}"] = $this->setContent;
                    } elseif ($content === 'token') {
                        $this->content["{{{$content}}}"] = App::$app->session->hashToken;
                    } else {
                        throw new NotFoundException("loi render layout");
                    }
                }
            }
            $this->renderContent();
        } else {
            throw new NotFoundException("layout error");
        }
    }

    public function renderContent()
    {
        $search = array_keys($this->content);
        $content = array_values($this->content);
        echo str_replace($search, $content, $this->fileLayout);
    }

    public function render($content = null)
    {

        $this->setContent = $this->file($content);
        $this->renderLayout();
    }

    public function __destruct()
    {
    }
}
