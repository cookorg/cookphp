<?php

namespace cook\database\orm\statement;

use cook\database\orm\clause\Limit;
use cook\database\orm\clause\Order;
use cook\database\orm\clause\Where;
use cook\database\Db;

/**
 * Class statement
 */
abstract class Statement {

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $placeholders = [];

    /**
     * @var
     */
    protected $table;

    /**
     * @var Where
     */
    protected $Where;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var Limit
     */
    protected $Limit;

    /**
     *
     * @var Db
     */
    protected $db;

    /**
     * @param Where $where
     * @param Order $order
     * @param Limit $limit
     */
    public function __construct(Where $where, Order $order, Limit $limit, Db $db) {
        $this->Where = $where;
        $this->Order = $order;
        $this->Limit = $limit;
        $this->db = $db;
    }

    /**
     * @param $table
     * @return $this
     */
    public function from($table) {
        $this->setTable($table);
        return $this;
    }

    /**
     * @param $column
     * @param null   $operator
     * @param null   $value
     * @param string $chainType
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $chainType = 'AND') {
        $this->values[] = $value;
        $this->Where->where($this->db->name($column), $operator, $chainType);
        return $this;
    }

    /**
     * AND编组开始
     * @param string $chainType
     * @return $this
     */
    public function groupStart($chainType = 'AND') {
        $this->Where->group(' ' . $chainType . ' (');
        return $this;
    }

    /**
     * OR编组开始
     * @param string $chainType
     * @return $this
     */
    public function orGroupStart($chainType = 'OR') {
        $this->Where->group(' ' . $chainType . ' (');
        return $this;
    }

    /**
     * 编组结束
     * @return $this
     */
    public function groupEnd() {
        $this->Where->group(' ) ');
        return $this;
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null) {
        $this->values[] = $value;
        $this->Where->orWhere($this->db->name($column), $operator);
        return $this;
    }

    /**
     * @param $column
     * @param array  $values
     * @param string $chainType
     * @return $this
     */
    public function whereBetween($column, array $values, $chainType = 'AND') {
        $this->setValues($values);
        $this->Where->whereBetween($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function orWhereBetween($column, array $values) {
        $this->setValues($values);
        $this->Where->orWhereBetween($this->db->name($column));
        return $this;
    }

    /**
     * @param $column
     * @param array  $values
     * @param string $chainType
     * @return $this
     */
    public function whereNotBetween($column, array $values, $chainType = 'AND') {
        $this->setValues($values);
        $this->Where->whereNotBetween($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function orWhereNotBetween($column, array $values) {
        $this->setValues($values);
        $this->Where->orWhereNotBetween($this->db->name($column));
        return $this;
    }

    /**
     * @param $column
     * @param array  $values
     * @param string $chainType
     * @return $this
     */
    public function whereIn($column, array $values, $chainType = 'AND') {
        $this->setValues($values);
        $this->setPlaceholders($values);
        $this->Where->whereIn($this->db->name($column), $this->getPlaceholders(), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function orWhereIn($column, array $values) {
        $this->setValues($values);
        $this->setPlaceholders($values);
        $this->Where->orWhereIn($this->db->name($column), $this->getPlaceholders());
        return $this;
    }

    /**
     * @param $column
     * @param array  $values
     * @param string $chainType
     * @return $this
     */
    public function whereNotIn($column, array $values, $chainType = 'AND') {
        $this->setValues($values);
        $this->setPlaceholders($values);
        $this->Where->whereNotIn($this->db->name($column), $this->getPlaceholders(), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return $this
     */
    public function orWhereNotIn($column, array $values) {
        $this->setValues($values);
        $this->setPlaceholders($values);
        $this->Where->orWhereNotIn($this->db->name($column), $this->getPlaceholders());
        return $this;
    }

    /**
     * @param $column
     * @param null   $value
     * @param string $chainType
     * @return $this
     */
    public function whereLike($column, $value = null, $chainType = 'AND') {
        $this->values[] = $value;
        $this->Where->whereLike($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param null $value
     * @return $this
     */
    public function orWhereLike($column, $value = null) {
        $this->values[] = $value;
        $this->Where->orWhereLike($this->db->name($column));
        return $this;
    }

    /**
     * @param $column
     * @param null   $value
     * @param string $chainType
     * @return $this
     */
    public function whereNotLike($column, $value = null, $chainType = 'AND') {
        $this->values[] = $value;
        $this->Where->whereNotLike($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param null $value
     * @return $this
     */
    public function orWhereNotLike($column, $value = null) {
        $this->values[] = $value;
        $this->Where->orWhereNotLike($this->db->name($column));
        return $this;
    }

    /**
     * @param $column
     * @param string $chainType
     * @return $this
     */
    public function whereNull($column, $chainType = 'AND') {
        $this->Where->whereNull($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function orWhereNull($column) {
        $this->Where->orWhereNull($this->db->name($column));
        return $this;
    }

    /**
     * @param $column
     * @param string $chainType
     * @return $this
     */
    public function whereNotNull($column, $chainType = 'AND') {
        $this->Where->whereNotNull($this->db->name($column), $chainType);
        return $this;
    }

    /**
     * @param $column
     * @return $this
     */
    public function orWhereNotNull($column) {
        $this->Where->orWhereNotNull($this->db->name($column));
        return $this;
    }

    /**
     * @param $columns
     * @param null   $operator
     * @param string $chainType
     * @return $this
     */
    public function whereMany($columns, $operator = null, $chainType = 'AND') {
        $this->values = array_merge($this->values, array_values($columns));
        $this->Where->whereMany(array_keys($columns), $operator, $chainType);
        return $this;
    }

    /**
     * @param $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->Order->orderBy($this->db->name($column), $direction);
        return $this;
    }

    /**
     * @param int $number
     * @param int $page
     * @return $this
     */
    public function limit($number, $page = null) {
        $this->Limit->limit($number, $page);
        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function getSql();

    /**
     * @param $table
     * @return $this
     */
    protected function setTable($table) {
        $this->table = $this->db->table($table);
        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    protected function setColumns(array $columns) {
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    protected function setValues(array $values) {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    /**
     * @return string
     */
    protected function getPlaceholders() {
        $placeholders = $this->placeholders;
        $this->placeholders = [];
        return '( ' . implode(' , ', $placeholders) . ' )';
    }

    /**
     * @param array $values
     */
    protected function setPlaceholders(array $values) {
        foreach ($values as $value) {
            $this->placeholders[] = $this->setPlaceholder('?', !is_array($value) ? 1 : count($value));
        }
    }

    /**
     * @param array $array
     * @return bool
     */
    protected function isAssociative(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * @param $text
     * @param int    $count
     * @param string $separator
     * @return string
     */
    private function setPlaceholder($text, $count = 0, $separator = ' , ') {
        $result = [];
        if ($count > 0) {
            for ($x = 0; $x < $count; ++$x) {
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }

}
