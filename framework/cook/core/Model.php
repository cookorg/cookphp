<?php

namespace cook\core;

use cook\database\Db;
use cook\http\Input;

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
     * 输入类
     * @var Input
     */
    public $input;

    /**
     * 表名称
     * @var string
     */
    public $form;

    public function __construct(Db $db, Input $input) {
        $this->db = $db;
        $this->input = $input;
        $this->form = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", ($pos = strrpos(($name = get_class($this)), '\\')) !== false ? substr($name, $pos + 1) : $name), '_'));
    }

    /**
     * 得到分表名
     * @param string|int $value
     * @param int $num
     * @return string
     */
    public function getPartition($value, $num = 10) {
        return $this->form . '_' . (is_numeric($value) ? ($value % $num) : (is_string($value) ? (ord(substr(md5($value), 0, 1)) % $num) : (ord($value{0}) % $num)));
    }

}
