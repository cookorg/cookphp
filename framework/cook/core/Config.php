<?php

namespace cook\core;

/**
 * 配制类
 * @author cookphp <admin@cookphp.org>
 */
class Config {

    /**
     * 配制
     * @var array 
     */
    protected static $confs = [];

    /**
     * 获取配制
     * @param string $key 配制名称
     * @param mixed $default 默认
     * @return mixed
     */
    public static function get(string $key, $default = null) {
        $name = strpos($key, '.') !== false ? strstr($key, '.', true) : $key;
        !isset(self::$confs[$name]) && self::load($name);
        return self::getNested(self::$confs, explode('.', $key), $default);
    }

    /**
     * 设置配制
     * @param string|array $key 配制名称
     * @param mixed $value 值
     * @param mixed $value
     */
    public static function set($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $value) {
                self::$confs = $value;
            }
        } else {
            self::$confs[$key] = $value;
        }
    }

    /**
     * 获取嵌入配制
     * @param array $data
     * @param array $keys
     * @param mixed $default
     * @return mixed
     */
    private static function getNested(array &$data, array $keys, $default = null) {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data = &$data[$key];
            } else {
                return $default;
            }
        }
        return $data ?: $default;
    }

    /**
     * 注册配制
     * @param string $name 配制文件
     */
    public static function load($name) {
        self::$confs[$name] = is_file(($filename = CONFIGPATH . $name . '.php')) ? require $filename : null;
    }

}
