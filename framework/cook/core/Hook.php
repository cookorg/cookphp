<?php

namespace cook\core;

use cook\core\Invoke;

/**
 * 事件钩子
 * @author cookphp <admin@cookphp.org>
 */
class Hook {

    /** @var array */
    protected $hooks = [];

    /**
     * 执行函数、类
     * @var Invoke
     */
    protected $invoke;

    public function __construct(Invoke $invoke) {
        $this->invoke = $invoke;
    }

    /**
     * 绑定事件
     * @param string   $event 事件名称
     * @param callable|string $callable 一个函数或函数名称
     */
    public function on($event, $callable) {
        $this->hooks[$event][] = $callable;
    }

    /**
     * 清除事件
     * @param string $event 事件名称
     */
    public function clear($event) {
        unset($this->hooks[$event]);
    }

    /**
     * 触发事件
     * @param string     $event 事件名称
     * @param null|mixed $payload 额外参数
     */
    public function trigger($event, &$payload = null) {
        if (isset($this->hooks[$event])) {
            foreach ($this->hooks[$event] as $closure) {
                $this->invoke->call($closure, $payload);
            }
        }
    }

}
