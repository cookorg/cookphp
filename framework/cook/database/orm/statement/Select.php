<?php

namespace cook\database\orm\statement;

use cook\database\orm\clause\Limit;
use cook\database\orm\clause\Order;
use cook\database\orm\clause\Where;
use cook\database\orm\clause\Group;
use cook\database\orm\clause\Having;
use cook\database\orm\clause\Join;
use cook\database\orm\clause\Offset;
use cook\database\Db;

/**
 * 查询类
 */
class Select extends Statement {

    /**
     * @var bool
     */
    protected $distinct = false;

    /**
     * @var bool
     */
    protected $aggregate = false;

    /**
     * @var Join
     */
    protected $Join;

    /**
     * @var Group
     */
    protected $Group;

    /**
     * @var Having
     */
    protected $Having;

    /**
     * @var Offset
     */
    protected $Offset;

    public function __construct(Where $where, Order $order, Limit $limit, Db $db, Join $join, Group $group, Having $having, Offset $offset) {
        parent::__construct($where, $order, $limit, $db);
        $this->Join = $join;
        $this->Group = $group;
        $this->Having = $having;
        $this->Offset = $offset;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function columns($columns) {
        $this->setColumns(is_array($columns) ? $columns : explode(',', $columns));
        return $this;
    }

    /**
     * @return $this
     */
    public function distinct() {
        $this->distinct = true;
        return $this;
    }

    /**
     * @param string $column
     * @param null   $as
     * @param bool   $distinct
     *
     * @return $this
     */
    public function count($column = '*', $as = null, $distinct = false) {
        $this->aggregate = true;
        $this->columns[] = $this->setDistinct($distinct) . ' ' . $this->db->name($column) . ' )' . $this->setAs($as);
        return $this;
    }

    /**
     * @param string $column
     * @param null   $as
     *
     * @return $this
     */
    public function distinctCount($column = '*', $as = null) {
        $this->count($column, $as, true);
        return $this;
    }

    /**
     * @param $column
     * @param null $as
     *
     * @return $this
     */
    public function max($column, $as = null) {
        $this->aggregate = true;
        $this->columns[] = 'MAX( ' . $this->db->name($column) . ' )' . $this->setAs($as);
        return $this;
    }

    /**
     * @param $column
     * @param null $as
     *
     * @return $this
     */
    public function min($column, $as = null) {
        $this->aggregate = true;
        $this->columns[] = 'MIN( ' . $this->db->name($column) . ' )' . $this->setAs($as);
        return $this;
    }

    /**
     * @param $column
     * @param null $as
     *
     * @return $this
     */
    public function avg($column, $as = null) {
        $this->aggregate = true;
        $this->columns[] = 'AVG( ' . $this->db->name($column) . ' )' . $this->setAs($as);
        return $this;
    }

    /**
     * @param $column
     * @param null $as
     *
     * @return $this
     */
    public function sum($column, $as = null) {
        $this->aggregate = true;
        $this->columns[] = 'SUM( ' . $this->db->name($column) . ' )' . $this->setAs($as);
        return $this;
    }

    /**
     * @param $table
     * @param $first
     * @param null   $operator
     * @param null   $second
     * @param string $joinType
     *
     * @return $this
     */
    public function join($table, $first, $operator = null, $second = null, $joinType = 'INNER') {
        $this->Join->join($this->db->table($table), $this->db->name($first), $operator, $this->db->name($second), $joinType);
        return $this;
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     *
     * @return $this
     */
    public function leftJoin($table, $first, $operator = null, $second = null) {
        $this->Join->leftJoin($this->db->table($table), $this->db->name($first), $operator, $this->db->name($second));
        return $this;
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     *
     * @return $this
     */
    public function rightJoin($table, $first, $operator = null, $second = null) {
        $this->Join->rightJoin($this->db->table($table), $this->db->name($first), $operator, $this->db->name($second));
        return $this;
    }

    /**
     * @param $table
     * @param $first
     * @param null $operator
     * @param null $second
     *
     * @return $this
     */
    public function fullJoin($table, $first, $operator = null, $second = null) {
        $this->Join->fullJoin($this->db->table($table), $this->db->name($first), $operator, $this->db->name($second));
        return $this;
    }

    /**
     * @param $columns
     *
     * @return $this
     */
    public function groupBy($columns) {
        $this->Group->groupBy($this->db->name($columns));
        return $this;
    }

    /**
     * @param $column
     * @param null   $operator
     * @param null   $value
     * @param string $chainType
     *
     * @return $this
     */
    public function having($column, $operator = null, $value = null, $chainType = 'AND') {
        $this->values[] = $value;
        $this->Having->having($this->db->name($column), $operator, $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function orHaving($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->orHaving($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingCount($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->havingCount($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingMax($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->havingMax($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingMin($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->havingMin($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingAvg($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->havingAvg($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function havingSum($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Having->havingSum($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function offset($number) {
        $this->Offset->offset($number);
        return $this;
    }

    /**
     * @return string
     */
    public function getSql() {
        if (empty($this->table)) {
            trigger_error('No table is set for selection', E_USER_ERROR);
        }

        $sql = $this->getSelect() . ' ' . $this->getColumns();
        $sql .= ' FROM ' . $this->table;
        $sql .= $this->Join;
        $sql .= $this->Where;
        $sql .= $this->Group;
        $sql .= $this->Having;
        $sql .= $this->Order;
        $sql .= $this->Limit;
        $sql .= $this->Offset;
        //echo $sql.PHP_EOL;
        return $sql;
    }

    /**
     * @return string
     */
    protected function getSelect() {
        if ($this->distinct) {
            return 'SELECT DISTINCT';
        }
        return 'SELECT';
    }

    /**
     * @return string
     */
    protected function getColumns() {
        return $this->db->name($this->columns ?: ['*']);
    }

    /**
     * @param $distinct
     *
     * @return string
     */
    protected function setDistinct($distinct) {
        if ($distinct) {
            return 'COUNT( DISTINCT';
        }
        return 'COUNT(';
    }

    /**
     * @param $as
     *
     * @return string
     */
    protected function setAs($as) {
        if (empty($as)) {
            return '';
        }
        return ' AS ' . $this->db->name($as);
    }

    /**
     *  执行一条预处理语句
     * @return \PDOstatement
     */
    public function execute() {
        //print_r($this->values);        print_r($this->getSql());exit;
        return $this->db->query($this->getSql(), $this->values);
    }

}
