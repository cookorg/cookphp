<?php

namespace cook\cache\driver;

use cook\cache\Driver;

/**
 * Apc缓存驱动
 * @author YoPHP <admin@YoPHP.org>
 */
class Apc extends Driver {

    public function enabled(): bool {
        return function_exists('apc_cache_info');
    }

    public function has($name): bool {
        return apc_exists($this->filename($name));
    }

    public function get($name) {
        return apc_fetch($this->filename($name));
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        return apc_store($this->filename($name), $value, $expire);
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return apc_delete($filename) || !apc_exists($filename);
    }

    public function inc($name, $step = 1) {
        return apc_inc($this->filename($name), intval($step));
    }

    public function dec($name, $step = 1) {
        return apc_dec($this->filename($name), intval($step));
    }

    public function clear() {
        return apc_clear_cache() && apc_clear_cache('user');
    }

}
