<?php

namespace cook\core;

/**
 * 时间基准
 * @author cookphp <admin@cookphp.org>
 */
class Benchmark {

    /**
     * @var array
     */
    public static $time = [];

    /**
     * @var array
     */
    public static $memory = [];

    /**
     * 标记时间
     * @param	string	$name
     * @return	$this
     */
    public static function markTime($name) {
        self::$time[$name] = microtime(true);
    }

    /**
     * 标记内存
     * @param	string	$name
     * @return	$this
     */
    public static function markMemory($name) {
        self::$memory[$name] = memory_get_usage(false);
    }

    /**
     * 计算两个标记点之间的时间差
     * @param string $pointa 开始标记
     * @param string $pointb 结束标记
     * @param int $decimals 小数位数
     * @return string
     */
    public static function elapsedTime($pointa = '', $pointb = '', $decimals = 4) {
        return number_format((self::$time[$pointb] ?? microtime(true)) - (self::$time[$pointa] ?? microtime(true)), $decimals);
    }

    /**
     * 计算两个标记点之间的内存差
     * @param string $pointa 开始标记
     * @param string $pointb 结束标记
     * @return string
     */
    public static function elapsedMemory($pointa = '', $pointb = '') {
        return round(((self::$memory[$pointb] ?? memory_get_usage(false)) - (self::$memory[$pointa] ?? memory_get_usage(false))) / 1024 / 1024, 2) . 'MB';
    }

}
