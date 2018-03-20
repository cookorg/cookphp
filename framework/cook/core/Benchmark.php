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
    public $time = [];

    /**
     * @var array
     */
    public $memory = [];

    /**
     * 标记时间
     * @param	string	$name
     * @return	$this
     */
    public function markTime($name) {
        $this->time[$name] = microtime(true);
        return $this;
    }

    /**
     * 标记内存
     * @param	string	$name
     * @return	$this
     */
    public function markMemory($name) {
        $this->memory[$name] = memory_get_usage(false);
        return $this;
    }

    /**
     * 计算两个标记点之间的时间差
     * @param string $pointa 开始标记
     * @param string $pointb 结束标记
     * @param int $decimals 小数位数
     * @return string
     */
    public function elapsedTime($pointa = '', $pointb = '', $decimals = 4) {
        return number_format(($this->time[$pointb] ?? microtime(true)) - ($this->time[$pointa] ?? microtime(true)), $decimals);
    }

    /**
     * 计算两个标记点之间的内存差
     * @param string $pointa 开始标记
     * @param string $pointb 结束标记
     * @return string
     */
    public function elapsedMemory($pointa = '', $pointb = '') {
        return round((($this->memory[$pointb] ?? memory_get_usage(false)) - ($this->memory[$pointa] ?? memory_get_usage(false))) / 1024 / 1024, 2) . 'MB';
    }

}
