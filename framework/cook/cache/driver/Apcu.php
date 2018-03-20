<?php

namespace cook\cache\driver;

use cook\cache\Driver;

/**
 * Apcu缓存驱动
 * @author YoPHP <admin@YoPHP.org>
 */
class Apcu extends Driver {

    public function enabled(): bool {
        return function_exists('apcu_cache_info');
    }

    public function has($name): bool {
        return apcu_exists($this->filename($name));
    }

    public function get($name) {
        return apcu_fetch($this->filename($name));
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        return apcu_store($this->filename($name), $value, $expire);
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return apcu_delete($filename) || !apcu_exists($filename);
    }

    public function inc($name, $step = 1) {
        return apcu_inc($this->filename($name), intval($step));
    }

    public function dec($name, $step = 1) {
        return apcu_dec($this->filename($name), intval($step));
    }

    public function clear() {
        return apcu_clear_cache() && apcu_clear_cache('user');
    }

}
