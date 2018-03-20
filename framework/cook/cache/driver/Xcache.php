<?php

namespace cook\cache\driver;

use cook\cache\Driver;

/**
 * Xcache缓存驱动
 * @author YoPHP <admin@YoPHP.org>
 * @link http://xcache.lighttpd.net/ xcache
 */
class Xcache extends Driver {

    public function enabled(): bool {
        return function_exists('xcache_info');
    }

    public function has($name): bool {
        return xcache_isset($this->filename($name));
    }

    public function get($name, $default = null) {
        $key = $this->filename($name);
        return xcache_isset($key) ? xcache_get($key) : $default;
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        return xcache_set($this->filename($name), $value, $expire) ? true : false;
    }

    public function inc($name, $step = 1) {
        return xcache_inc($this->filename($name), intval($step));
    }

    public function dec($name, $step = 1) {
        return xcache_dec($this->filename($name), intval($step));
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return !xcache_isset($filename) || xcache_unset($filename);
    }

    public function clear() {
        return xcache_clear_cache(XC_TYPE_VAR);
    }

}
