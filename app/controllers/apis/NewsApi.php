<?php

namespace app\controllers\apis;

use app\core\Controller;
use app\core\controllers\ControllerApi;
use app\models\NewsModel;
use app\core\Request;
use app\core\Response;
use app\core\Test;
use phpseclib\Crypt\Twofish;

class NewsApi extends ControllerApi
{
    public $result = [];

    public function __construct()
    {
        $this->NewsModel = new NewsModel();
    }

    /* abstract */
    protected function actionsMiddle(): array
    {
        return [
            'admin' => ['update', 'delete', 'insert'],
            'token' =>  ['update', 'delete', 'insert'],
        ];
    }

    public function setResult(): array
    {
        return $this->result;
    }
    /*   end abstract */

    public function index(Request $request)
    {
        $action = $request->getBody()['news_id'];
        $this->result[] = $this->NewsModel->find(['news_id' => $action]);
    }

    public function insert(Request $request)
    {
        if ($this->NewsModel->validate($request->getBody())) {
            $this->result[] = $this->NewsModel->_save();
        } else {
            $this->result[] = $this->NewsModel->errors;
            $this->result[] = 412;
        }
    }

    public function update(Request $request)
    {
        $id = $request->getBody()['id'];
        if ($this->NewsModel->validate($request->getBody())) {
            $this->NewsModel->_update(['id' => $id]);
        } else {
            $this->result[] = $this->NewsModel->errors;
            $this->result[] = 412;
        }
    }

    public function detail(Request $request, Response $response)
    {
        foreach ($request->getBody() as $attr => $vlaue) {
            if ($attr == 'path') {
                $where = ['path' => $request->getBody()[$attr]];
                $id = $this->NewsModel->fetch($where, 'url_seo')['id'];
                $this->result[] = $this->NewsModel->findOne(['url_id' => $id]);
            } elseif ($attr == 'id') {
                $this->result[] = $this->NewsModel->findOne(['id' => $request->getBody()[$attr]]);
            }
        }
    }

    public function delete(Request $request)
    {
        $id = $request->getBody()['id'];
        $this->NewsModel->_delete(['id' => $id]);
    }

    public function show(Request $request)
    {
        $news = $request->getBody()['news'];
        // $this->result[] = $news['where'];
        $this->result[] = $this->NewsModel->filter($news);
    }

    public function category()
    {
        $this->result[] = $this->NewsModel->test('all', 'news');
    }
}
