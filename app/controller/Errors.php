<?php

namespace app\controller;

use cook\http\Output;

/**
 * 错误类
 */
class Errors {

    /**
     * @var Output
     */
    public $output;

    public function __construct(Output $output) {
        $this->output = $output;
    }

    public function errors($code) {
       $this->output->setStatusHeader($code)->display();
    }

}
