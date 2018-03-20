<?php

namespace cook\database\orm\clause;

/**
 * Class Order
 */
class Order extends Container {

    /**
     * @param $column
     * @param string $direction
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->container[] = $column . ' ' . strtoupper($direction);
    }

    /**
     * @return string
     */
    public function __toString() {
        if (empty($this->container)) {
            return '';
        }
        $container = $this->container;
        $this->container = [];
        return ' ORDER BY ' . implode(' , ', $container);
    }

}
