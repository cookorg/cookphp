<?php

namespace cook\cache\driver;

use cook\cache\Driver;

/**
 * Redis缓存驱动
 * @author YoPHP <admin@YoPHP.org>
 */
class Redis extends Driver {

    /**
     * @var void 
     */
    private $handler = null;

    /**
     * @var array 
     */
    protected $configBase = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'expire' => 0,
        'persistent' => false,
        'prefix' => ''
    ];

    public function enabled(): bool {
        if (!extension_loaded('Redis')) {
            return false;
        }
        $this->configBase += $this->config->get('redis', []);
        $func = $this->configBase['persistent'] ? 'pconnect' : 'connect';
        $this->handler = new \Redis;
        $this->handler->$func($this->configBase['host'], $this->configBase['port'], $this->configBase['timeout']);
        !empty($this->configBase['password']) && $this->handler->auth($this->configBase['password']);
        if (0 != $this->configBase['select']) {
            $this->handler->select($this->configBase['select']);
        }
        return true;
    }

    public function has($name): bool {
        return $this->handler->get($this->filename($name)) ? true : false;
    }

    public function get($name, $default = null) {
        $value = $this->handler->get($this->filename($name));
        if (is_null($value)) {
            return $default;
        }
        return $value;
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        $key = $this->filename($name);
        return is_int($expire) && $expire ? $this->handler->setex($key, $expire, $value) : $this->handler->set($key, $value);
    }

    public function inc($name, $step = 1) {
        return $this->handler->incrby($this->filename($name), intval($step));
    }

    public function dec($name, $step = 1) {
        return $this->handler->decrby($this->filename($name), intval($step));
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return boolval(!$this->handler->get($filename) || $this->handler->delete($filename));
    }

    public function clear() {
        return $this->handler->flushDB();
    }

}
