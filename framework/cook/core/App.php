<?php

namespace cook\core;

use cook\core\Benchmark;
use cook\core\Config;
use cook\core\Container as DI;
use cook\core\Invoke;
use cook\router\Router;
use cook\router\RouterBase;
use Throwable;

/**
 * 应用处理
 * @author cookphp <admin@cookphp.org>
 */
class App {

    /**
     * 时间基准
     * @var Benchmark
     */
    public $benchmark;

    /**
     * 应用配置
     * @var Config
     */
    public $config;

    /**
     * 执行函数、类
     * @var Invoke
     */
    public $invoke;

    /**
     * 控制器
     * @var Route
     */
    public $route;

    /**
     * 路由
     * @var Router
     */
    public $router;

    public function __construct(Benchmark $benchmark, Config $config, Invoke $invoke, Router $router) {
        $this->benchmark = $benchmark->markTime('start_time')->markMemory('start_memory');
        $this->config = $config;
        $this->invoke = $invoke;
        $this->router = $router;
    }

    /**
     * 启动应用程序
     */
    public function run() {
        $this->initialize();
        $this->initroute();
    }

    /**
     * 处理环境设置
     */
    protected function initialize() {
        //设置默认时区
        date_default_timezone_set($this->config->app['timezone'] ?? 'PRC');
    }

    /**
     * 处理路由
     */
    protected function initroute() {
       // try {
            if (class_exists('\\' . APPNAMESPACE . '\\Router')) {
                $router = DI::get('\\' . APPNAMESPACE . '\\Router');
                if ($router instanceof RouterBase) {
                    $router->start();
                    ($this->router->matchUrl() instanceof Router) ? (!empty($this->router->route['controller']) && !empty($this->router->route['action']) ? $this->invoke->method($this->router->route['controller'], $this->router->route['action'], $this->router->getValues()) : (!empty($this->router->route['controller']) ? $this->invoke->call($this->router->route['controller'], $this->router->getValues()) : null)) : $this->showError(404);
                }
            } else {
                $this->showError(500);
            }
       // } catch (Throwable $e) {
            //print_r($e);
         //   throw $e;
       // }
    }

    public function showError($code) {
        exit;
    }

}
