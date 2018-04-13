<?php

namespace cook\cache\driver;

use cook\cache\Driver;

class Memcached extends Driver {

    /**
     * @var void 
     */
    private $handler = null;
    protected $configBase = [
        'host' => '127.0.0.1',
        'port' => 11211,
        'expire' => 0,
        'timeout' => 0, // 超时时间（单位：毫秒）
        'prefix' => '',
        'username' => '', //账号
        'password' => '', //密码
        'option' => [],
    ];

    public function enabled(): bool {
        if (!extension_loaded('memcached')) {
            return false;
        }
        $this->configBase += $this->config->get('memcached', []);
        $this->handler = new \Memcached;
        if (!empty($thi['option'])) {
            $this->handler->setOptions($this->configBase['option']);
        }
        // 设置连接超时时间（单位：毫秒）
        if ($this->configBase['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->configBase['timeout']);
        }
        // 支持集群
        $hosts = explode(',', $this->configBase['host']);
        $ports = explode(',', $this->configBase['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array) $hosts as $i => $host) {
            $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
        }
        $this->handler->addServers($servers);
        if ('' != $this->configBase['username']) {
            $this->handler->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $this->handler->setSaslAuthData($this->configBase['username'], $this->configBase['password']);
        }
        return true;
    }

    public function has($name) {
        $key = $this->filename($name);
        return $this->handler->get($key) ? true : false;
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
       
        $key = $this->filename($name);
        $expire = 0 == $expire ? 0 : time() + $expire;
        if ($this->handler->set($key, $value, $expire)) {
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
