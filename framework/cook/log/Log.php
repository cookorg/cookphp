<?php

namespace cook\log;

use cook\core\Config;
use cook\log\Driver;
use cook\core\Container as DI;

/**
 * 日志记录
 * @author cookphp <admin@cookphp.org>
 */
class Log {

    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    const SQL = 'sql';

    public static $logs = [];

    /**
     * @var Driver
     */
    private static $handler = null;

    /**
     * 系统不可用
     * @param string $message
     * @param array  $context
     * @return void
     */
    public static function emergency($message, array $context = []) {
        self::log(self::EMERGENCY, $message, $context);
    }

    /**
     * **必须**立刻采取行动
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert($message, array $context = []) {
        self::log(self::ALERT, $message, $context);
    }

    /**
     * 紧急情况
     * 例如：程序组件不可用或者出现非预期的异常
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical($message, array $context = []) {
        self::log(self::CRITICAL, $message, $context);
    }

    /**
     * 运行时出现的错误，不需要立刻采取行动，但必须记录下来以备检测
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($message, array $context = []) {
        self::log(self::ERROR, $message, $context);
    }

    /**
     * 出现非错误性的异常
     * 例如：使用了被弃用的API、错误地使用了API或者非预想的不必要错误
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($message, array $context = []) {
        self::log(self::WARNING, $message, $context);
    }

    /**
     * 一般性重要的事件
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice($message, array $context = []) {
        self::log(self::NOTICE, $message, $context);
    }

    /**
     * 重要事件
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($message, array $context = []) {
        self::log(self::INFO, $message, $context);
    }

    /**
     * debug 详情
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug($message, array $context = []) {
        self::log(self::DEBUG, $message, $context);
    }

    /**
     * SQL记录
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function sql($message, array $context = []) {
        self::log(self::SQL, $message, $context);
    }

    /**
     * 任意等级的日志记录
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log($level, $message, array $context = []) {
        self::$logs[$level][] = $message;
        in_array($level, Config::get('log.logger')) && self::setLogger($level, self::interpolate($message, $context));
    }

    /**
     * 保存日志
     * @param string $level
     * @param string $message
     */
    public static function setLogger($level, $message) {
        !self::$handler && self::connect();
        self::$handler->write($level, $message);
    }

    /**
     * 连接驱动
     * @return $this
     */
    private static function connect() {
        if (!self::$handler) {
            $driver = Config::get('log.driver', 'File');
            $class = false !== strpos($driver, '\\') ? $driver : 'cook\\log\\driver\\' . ucwords($driver);
            self::$handler = DI::get($class);
            if (!(self::$handler instanceof Driver)) {
                throw new Exception('Error Log Handler:' . $class);
            }
        }
    }

    protected static function interpolate($message, array $context = []) {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

}
