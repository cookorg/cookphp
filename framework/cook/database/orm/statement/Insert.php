<?php

namespace cook\database\orm\statement;

/**
 * 新增类
 */
class Insert extends Statement {

    /**
     * @var bool
     */
    protected $replace = false;

    public function data(array $data) {
        if (!empty($data)) {
            $this->columns(array_keys($data));
            $this->values(array_values($data));
        }
        return $this;
    }

    public function datas(array $datas) {
        if (!empty($datas[0])) {
            $this->columns(array_keys($datas[0]));
            foreach ($datas as $value) {
                $this->setValues(array_values($value));
            }
            $this->setPlaceholders($datas);
        }
        return $this;
    }

    public function replace($replace = true) {
        $this->replace = $replace;
        return $this;
    }

    /**
     * @return string
     */
    protected function getPlaceholders() {
        $placeholders = $this->placeholders;
        $this->placeholders = [];
        $toArray = [];
        foreach ($placeholders as $value) {
            if (strpos($value, ',') !== false) {
                $toArray[] = '( ' . $value . ' )';
            } else {
                $toArray[] = $value;
            }
        }
        $to = trim(implode(' , ', $toArray), '()');
        return '( ' . $to . ' )';
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns) {
        for ($index = 0; $index < count($columns); $index++) {
            $columns[$index] = $this->db->name($columns[$index]);
        }
        $this->setColumns($columns);
        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function values(array $values) {
        $this->setValues($values);
        $this->setPlaceholders($values);
        return $this;
    }

    /**
     * @return string
     */
    public function getSql() {
        if (empty($this->table)) {
            trigger_error('没有设置要插入的表格', E_USER_ERROR);
        }
        if (empty($this->columns)) {
            trigger_error('缺少要插入的列', E_USER_ERROR);
        }
        if (empty($this->values)) {
            trigger_error('缺少插入值', E_USER_ERROR);
        }
        $sql = ($this->replace ? 'REPLACE INTO ' : 'INSERT INTO ') . $this->table;
        $sql .= ' ' . $this->getColumns();
        $sql .= ' VALUES ' . $this->getPlaceholders();
        return $sql;
    }

    /**
     * @param bool $insertId
     *
     * @return \PDOstatement
     */
    public function execute($insertId = true) {
        $exec = $this->db->exec($this->getSql(), $this->values);
        return $insertId && $exec && !$this->replace ? $this->db->lastInsertId() : $exec->rowCount();
    }

    /**
     * @return string
     */
    protected function getColumns() {
        return '( ' . implode(' , ', $this->columns) . ' )';
    }

}
