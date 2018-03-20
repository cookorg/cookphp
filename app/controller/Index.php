<?php

namespace app\controller;

use cook\core\View;
use cook\database\Db;

class Index {

    /**
     * 应用配置
     * @var View
     */
    public $view;

    public function __construct(View $view) {
        $this->view = $view;
    }

    public function index(\app\model\Token $Token) {
        print_r($Token->getToken(1));
    }

    public function index2($id) {
        echo $id;
    }

}
