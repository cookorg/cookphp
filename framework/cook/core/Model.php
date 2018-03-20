<?php

namespace cook\core;

use cook\database\Db;

/**
 * 数据模型
 * @author cookphp <admin@cookphp.org>
 */
class Model {

    /**
     * 数据库类
     * @var Db
     */
    protected $db;

    public function __construct(Db $db) {
        $this->db = $db;
    }

}
