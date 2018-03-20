<?php

namespace App\controller;

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

    public function index(Db $db) {
        print_r($db->ORM->select('users')->where('uid', '=', 1)->limit(1)->execute()->fetchAll());
        //$this->view->display();
    }

    public function index2($id) {
        echo $id;
    }

}
