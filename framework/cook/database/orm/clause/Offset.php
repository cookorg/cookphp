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
        if (preg_match('/^[1-9][0-9]*$/i', $number) && $number >= 0) {
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
