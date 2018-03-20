<?php

namespace cook\router;

use cook\router\Router;

/**
 * 路由基础类
 * @author cookphp <admin@cookphp.org>
 */
abstract class RouterBase {

    /**
     * 应用配置
     * @var Router
     */
    protected $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * 开始路由
     */
    abstract public function start();
}
