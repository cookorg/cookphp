<?php

namespace library;

use cook\core\Config;

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

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
     * 开始
     */
    public function start() {
        ini_set('session.save_handler', $this->config->session['savehandler'] ?? 'files');
        ini_set('session.save_path', $this->config->session['savepath'] ?? WRITEPATH . 'session');
        session_start();
    }

    /**
     * 彻底终结
     */
    public function destroy() {
        session_destroy();
    }

}
