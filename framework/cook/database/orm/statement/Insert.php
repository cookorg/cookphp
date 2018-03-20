<?php

namespace cook\database\orm\statement;

/**
 * 新增类
 */
class Insert extends statement {

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
            $columns[$index] = $this->db->identifier($columns[$index]);
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
            trigger_error('No table is set for insertion', E_USER_ERROR);
        }
        if (empty($this->columns)) {
            trigger_error('Missing columns for insertion', E_USER_ERROR);
        }
        if (empty($this->values)) {
            trigger_error('Missing values for insertion', E_USER_ERROR);
        }

        $sql = ($this->replace ? 'REPLACE INTO ' : 'INSERT INTO ') . $this->table;
        $sql .= ' ' . $this->getColumns();
        $sql .= ' VALUES ' . $this->getPlaceholders();

        return $sql;
    }

    /**
     * @param bool $insertId
     *
     * @return string
     */
    public function execute($insertId = true) {
        $exec = $this->db->exec($this->getSql(), $this->values);
        return $insertId && !$this->replace ? $this->db->lastInsertId() : $exec;
    }

    /**
     * @return string
     */
    protected function getColumns() {
        return '( ' . implode(' , ', $this->columns) . ' )';
    }

}
