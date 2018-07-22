<?php

namespace cook\database\orm\clause;

/**
 * Class Container
 */
abstract class Container {

    /**
     * @var array
     */
    protected $container = [];

    /**
     * 数据库表达式
     * @var array 
     */
    protected $exp = ['eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<='];

}
