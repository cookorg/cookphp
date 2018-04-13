<?php

namespace cook\database\orm;

use cook\core\Container as DI;
use cook\database\orm\statement\Select;
use cook\database\orm\statement\Insert;
use cook\database\orm\statement\Update;
use cook\database\orm\statement\Delete;

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
        return DI::create(Select::class)->from($from);
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
        return DI::create(Insert::class)->from($from)->data($data);
    }

    /**
     * 批量创建新增实例
     * @param array $datas 数据
     * @param string $from 表
     * @return Insert
     */
    public function inserts(array $datas, $from = null) {
        return DI::create(Insert::class)->from($from)->datas($datas);
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
        return DI::create(Update::class)->from($from)->set($data);
    }

    /**
     * 创建删除实例
     * @param string $from 表
     * @return Delete
     */
    public function delete($from = null) {
        return DI::create(Delete::class)->from($from);
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
