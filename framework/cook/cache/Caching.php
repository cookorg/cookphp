<?php

namespace cook\cache;

use cook\core\Config;
use cook\core\Container as DI;
use cook\cache\Driver;

/**
 * 缓存类
 * @author YoPHP <admin@YoPHP.org>
 */
class Caching {

    /**
     * @var Driver
     */
    public static $handler = null;

    /**
     * 处理环境设置
     */
    protected static function initialize() {
        return self::$handler ?: (self::$handler = self::start());
    }

    /**
     * 初始连接缓存
     * @param string $driver 驱动
     * @return Driver
     */
    public static function start($driver = null) {
        $handler = DI::get('cook\\cache\\driver\\' . ucwords($driver ?: (Config::get('caching.driver') ?: 'File')));
        if ($handler instanceof Driver && $handler->enabled()) {
            return $handler;
        } else {
            throw new Exception('高速缓存启动失败');
        }
    }

    /**
     * 判断缓存是否存在
     * @param string $name 缓存变量名
     * @return bool
     */
    public static function has($name) {
        return self::initialize()->has($name);
    }

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public static function get($name, $default = null) {
        return self::initialize()->get($name, $default);
    }

    /**
     * 写入缓存
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return bool
     */
    public static function set($name, $value, $expire = null) {
        return self::initialize()->set($name, $value, $expire);
    }

    /**
     * 自增缓存（针对数值缓存）
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public static function inc($name, $step = 1) {
        return self::initialize()->inc($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public static function dec($name, $step = 1) {
        return self::initialize()->dec($name, $step);
    }

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    public static function rm($name) {
        return self::initialize()->rm($name);
    }

    /**
     * 清除缓存
     * @return bool
     */
    public static function clear() {
        return self::initialize()->clear();
    }

}
