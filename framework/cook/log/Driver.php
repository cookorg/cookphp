<?php

namespace cook\log;

/**
 * 日志接口
 * @author cookphp <admin@cookphp.org>
 */
abstract class Driver {

    /**
     * 写入日志
     * @param 级别 $level
     * @param 消息 $message
     * @return $this
     */
    abstract public function write($level, $message);
}
