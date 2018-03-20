<?php

namespace cook\database\orm;

use cook\core\Container as DI;
use cook\database\orm\statement\Select;
use cook\database\orm\statement\Insert;
use cook\database\orm\statement\Update;
use cook\database\orm\statement\Delete;
use cook\database\Db;

/**
 * 数据库查询
 * @author cook\database <admin@cook\database.org>
 */
class Query {

    /**
     * @var array
     */
    public $_value;

    /**
     * 创建查询实例
     * @param string $from 表
     * @return Select
     */
    public function select($from = null) {
        return DI::create(Select::class)->from($this->parsefrom($from));
    }

    /**
     * 创建新增实例
     * @param array $data 数据
     * @param string $from 表
     * @return Insert
     */
    public function insert(array $data = [], $from = null) {
        if (empty($data)) {
            $data = $this->toArray();
            $this->_data = [];
        }
        return DI::create(Insert::class)->from($this->parsefrom($from))->data($data);
    }

    /**
     * 批量创建新增实例
     * @param array $datas 数据
     * @param string $from 表
     * @return Insert
     */
    public function inserts(array $datas, $from = null) {
        return DI::create(Insert::class)->from($this->parsefrom($from))->datas($datas);
    }

    /**
     * 创建更新实例
     * @param array $data 数据
     * @param string $from 表
     * @return Update
     */
    public function update(array $data = [], $from = null) {
        if (empty($data)) {
            $data = $this->toArray();
            $this->_data = [];
        }
        return DI::create(Update::class)->from($this->parsefrom($from))->set($data);
    }

    /**
     * 创建删除实例
     * @param string $from 表
     * @return Delete
     */
    public function delete($from = null) {
        return DI::create(Delete::class)->from($this->parsefrom($from));
    }

    /**
     * 解析表
     * @param string  $form 表名
     * @return string
     */
    public function parsefrom($form) {
        if (empty($form)) {
            return null;
        }
        return DI::get(Db::class)->dbprefix . strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $form), '_'));
    }

    public function toArray() {
        return $this->_value;
    }

    public function __set($name, $value) {
        $this->_value[$name] = $value;
    }

    public function __get($name) {
        return $this->_value[$name] ?? null;
    }

}
