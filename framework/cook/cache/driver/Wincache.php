<?php

namespace cook\cache\driver;

use cook\cache\Driver;

/**
 * Wincache缓存驱动
 * @author YoPHP <admin@YoPHP.org>
 */
class Wincache extends Driver {

    public function enabled(): bool {
        return function_exists('apc_cache_info');
    }

    public function has($name): bool {
        return wincache_ucache_exists($this->filename($name));
    }

    public function get($name, $default = null) {
        $key = $this->filename($name);
        return wincache_ucache_exists($key) ? wincache_ucache_get($key) : $default;
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        return wincache_ucache_set($this->filename($name), $value, $expire);
    }

    public function inc($name, $step = 1) {
        return wincache_ucache_inc($this->filename($name), intval($step));
    }

    public function dec($name, $step = 1) {
        return wincache_ucache_dec($this->filename($name), intval($step));
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return !wincache_ucache_exists($filename) || wincache_ucache_delete($filename);
    }

    public function clear() {
        return wincache_ucache_clear();
    }

}
