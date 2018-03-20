<?php

namespace cook\database\orm\clause;

/**
 * Class Offset
 */
class Offset extends Container {

    /**
     * @var null
     */
    private $offset = null;

    /**
     * @param $number
     */
    public function offset($number) {
        if (!is_int($number)) {
            trigger_error('Expects parameter as integer', E_USER_ERROR);
        }

        if ($number >= 0) {
            $this->offset = intval($number);
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        if (is_null($this->offset)) {
            return '';
        }
        $offset = $this->offset;
        $this->offset = null;
        return ' OFFSET ' . $offset;
    }

}
