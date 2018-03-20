<?php

namespace cook\log\driver;

use cook\log\Driver;
use library\Path;


/**
 * 文件记录
 * @author cookphp <admin@cookphp.org>
 */
class File extends Driver {
/**
     * 应用配置
     * @var Path
     */
    public $path;

    public function __construct(Path $path) {
        $this->path = $path;
    }
    /**
     * 写入日志
     * @param 级别 $level
     * @param 消息 $message
     */
    public function write($level, $message) {
        $this->path->mkDir(($dir = WRITEPATH . 'logs' . DS . $level . DS . date('Y') . DS . date('m') . DS)) && is_writable($dir) && error_log('[' . date('Y-m-d H:i:s P') . '] ' . rtrim($message) . PHP_EOL, 3, $dir . date('d') . '.log');
    }

}
