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
     * 开始
     */
    public static function start() {
        ini_set('session.save_handler', Config::get('session.savehandler') ?: 'files');
        ini_set('session.save_path', Config::get('session.savepath') ?: WRITEPATH . 'session');
        ini_get('session.save_handler') === 'files' && Path::mkDir(ini_get('session.save_path'));
        session_start();
    }

    /**
     * 彻底终结
     */
    public static function destroy() {
        session_destroy();
    }

}
