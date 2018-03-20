<?php

namespace cook\database\ORM\Clause;

/**
 * Class Group
 */
class Group extends Container {

    /**
     * @param $columns
     */
    public function groupBy($columns) {
        $this->container[] = $columns;
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
        return ' GROUP BY ' . implode(' , ', $container);
    }

}
