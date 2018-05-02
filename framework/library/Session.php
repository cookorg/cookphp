<?php

namespace library;

use cook\core\Config;
use library\Path;

/**
 * Session类
 * @author cookphp <admin@cookphp.org>
 */
class Session {

    /**
     * 配制
     * @var Config
     */
    public $config;

    /**
     * 路径处理类
     * @var Path
     */
    public $path;

    public function __construct(Config $config, Path $path) {
        $this->config = $config;
        $this->path = $path;
    }

    /**
     * 开始
     */
    public function start() {
        ini_set('session.save_handler', $this->config->session['savehandler'] ?? 'files');
        ini_set('session.save_path', $this->config->session['savepath'] ?? WRITEPATH . 'session');
        ini_get('session.save_handler') === 'files' && $this->path->mkDir(ini_get('session.save_path'));
        session_start();
    }

    /**
     * 彻底终结
     */
    public function destroy() {
        session_destroy();
    }

}
