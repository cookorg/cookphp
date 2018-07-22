<?php

namespace cook\cache;

use cook\core\Config;

/**
 * 缓存接口
 * @author YoPHP <admin@YoPHP.org>
 */
abstract class Driver {

    /**
     * @var array 
     */
    protected $configBase = [
        'prefix' => '',
        'expire' => 3600,
    ];

    public function __construct() {
        $this->configBase['prefix'] = Config::get('caching.prefix', '');
        $this->configBase['expire'] = Config::get('caching.expire', 3600);
    }

    /**
     * 取得存储文件名
     * @param string $name 缓存变量名
     * @return string
     */
    protected function filename(string $name): string {
        return $this->configBase['prefix'] . md5($name);
    }

    /**
     * 检测是否可用
     * @return bool
     */
    abstract public function enabled();

    /**
     * 判断缓存是否存在
     * @param string $name 缓存变量名
     * @return bool
     */
    abstract public function has($name);

    /**
     * 读取缓存
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    abstract public function get($name, $default = null);

    /**
     * 写入缓存
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return bool
     */
    abstract public function set($name, $value, $expire = null);

    /**
     * 自增缓存（针对数值缓存）
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    abstract public function inc($name, $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    abstract public function dec($name, $step = 1);

    /**
     * 删除缓存
     * @param string $name 缓存变量名
     * @return bool
     */
    abstract public function rm($name);

    /**
     * 清除缓存
     * @return bool
     */
    abstract public function clear();
}
