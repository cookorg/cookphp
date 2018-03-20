<?php

namespace cook\cache\driver;

use cook\cache\Driver;

class Memcache extends Driver {

    /**
     * @var void 
     */
    private $handler = null;
    protected $configBase = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'expire' => 0,
        'timeout' => 0, // 超时时间（单位：毫秒）
        'persistent' => true,
        'prefix' => '',
    ];

    public function enabled(): bool {
        if (!extension_loaded('memcache')) {
            return false;
        }
        $this->configBase += $this->config->get('memcache', []);
        $this->handler = new \Memcache;
        // 支持集群
        $hosts = explode(',', $this->configBase['host']);
        $ports = explode(',', $this->configBase['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        foreach ((array) $hosts as $i => $host) {
            $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
            $this->configBase['timeout'] > 0 ? $this->handler->addServer($host, $port, $this->configBase['persistent'], 1, $this->configBase['timeout']) : $this->handler->addServer($host, $port, $this->configBase['persistent'], 1);
        }
        return true;
    }

    public function has($name) {
        return $this->handler->get($this->filename($name)) ? true : false;
    }

    public function get($name, $default = false) {
        $result = $this->handler->get($this->filename($name));
        return false !== $result ? $result : $default;
    }

    public function set($name, $value, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->configBase['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        $key = $this->filename($name);
        if ($this->handler->set($key, $value, 0, $expire)) {
            isset($first) && $this->setTagItem($key);
            return true;
        }
        return false;
    }

    public function inc($name, $step = 1) {
        $key = $this->filename($name);
        if ($this->handler->get($key)) {
            return $this->handler->increment($key, $step);
        }
        return $this->handler->set($key, $step);
    }

    public function dec($name, $step = 1) {
        $key = $this->filename($name);
        $value = $this->handler->get($key) - $step;
        $res = $this->handler->set($key, $value);
        if (!$res) {
            return false;
        } else {
            return $value;
        }
    }

    public function rm($name) {
        return $this->handler->delete($this->filename($name));
    }

    public function clear() {

        return $this->handler->flush();
    }

}
