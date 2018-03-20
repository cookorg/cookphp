<?php

namespace cook\core;

use cook\database\Db;

/**
 * 数据模型
 * @author cookphp <admin@cookphp.org>
 */
abstract class Model {

    /**
     * 数据库类
     * @var Db
     */
    public $db;

    /**
     * 表名称
     * @var string
     */
    public $form;

    public function __construct(Db $db) {
        $this->db = $db;
        $this->form = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", ($pos = strrpos(($name = get_class($this)), '\\')) !== false ? substr($name, $pos + 1) : $name), '_'));
    }

}
