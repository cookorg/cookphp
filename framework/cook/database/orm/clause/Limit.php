<?php

namespace cook\database\orm\clause;

/**
 * Class Limit
 */
class Limit extends Container {

    /**
     * @var null
     */
    private $limit = null;

    /**
     * @var string
     */
    private $pattern = '/^[1-9][0-9]*$/i';

    /**
     * @param int $number
     * @param int $page
     */
    public function limit($number, $page = null) {
        if (preg_match($this->pattern, $page)) {
            $offset = intval($number) * (intval($page) - 1);
        }
        if (!empty($offset) && preg_match($this->pattern, $offset) && preg_match($this->pattern, $number)) {
            $this->limit = intval($number) . ' OFFSET ' . intval($offset);
        } elseif (preg_match($this->pattern, $number)) {
            $this->limit = intval($number);
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        if (is_null($this->limit)) {
            return '';
        }
        $limit = $this->limit;
        $this->limit = null;
        return ' LIMIT ' . $limit;
    }

}
