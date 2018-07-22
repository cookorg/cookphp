<?php

namespace cook\core;

use cook\core\Benchmark;
use cook\core\Config;
use cook\core\Invoke;
use cook\router\Router;

/**
 * 应用处理
 * @author cookphp <admin@cookphp.org>
 */
class App {

    /**
     * 启动应用程序
     */
    public static function run() {
        Benchmark::markTime('start_time');
        Benchmark::markMemory('start_memory');
        self::initialize();
        self::initroute();
    }

    /**
     * 处理环境设置
     */
    protected static function initialize() {
        //设置默认时区
        date_default_timezone_set(Config::get('app.timezone', 'PRC'));
    }

    /**
     * 处理路由
     */
    protected static function initroute() {
        if (is_file(($filename = APPPATH . 'Router.php'))) {
            require_once $filename;
            Router::matchUrl() ? (!empty(Router::$route['controller']) && !empty(Router::$route['action']) ? Invoke::method(Router::$route['controller'], Router::$route['action'], Router::getValues()) : (!empty(Router::$route['controller']) ? Invoke::call(Router::$route['controller'], Router::getValues()) : null)) : exit();
        }
    }

}
