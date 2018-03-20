<?php

namespace cook\core;

use Exception;
use ReflectionClass;

/**
 * 实例容器
 * @author cookphp <admin@cookphp.org>
 */
class Container {

    /** @var array */
    protected static $instances = [];

    /** @var array */
    protected static $relations = [];

    /**
     * 获取已实例化的实例或创建一个新实例
     * @param string $className
     * @param string $parentClassName
     * @return mixed
     * @throws Exception
     */
    public static function get($className, $parentClassName = '') {
        $className = ltrim($className, '\\');
        if ($parentClassName) {
            self::$relations[$className][$parentClassName] = true;
        }
        if ($parentClassName && isset(self::$relations[$parentClassName]) && isset(self::$relations[$parentClassName][$className])) {
            $message = "发现循环实例: {$className} 互相循环依赖 {$parentClassName}";
            throw new Exception($message);
        }
        !self::isStored($className) && self::store(self::create($className));
        return self::$instances[$className];
    }

    /**
     * 创建一个新实例
     * @param string $className
     * @return mixed
     * @throws Exception
     */
    public static function create($className) {
        try {
            $reflection = new ReflectionClass($className);
        } catch (Exception $e) {
            throw $e;
        }
        $construct = $reflection->getConstructor();
        if ($construct) {
            $args = [];
            foreach ($construct->getParameters() as $value) {
                if (is_object($value->getClass())) {
                    $args[] = self::get($value->getClass()->getName(), $className);
                } else {
                    throw new Exception("无法创建实例:'{$className}',构造函数变量:'" . $value->getName() . ",不正确的声明");
                }
            }
            return $reflection->newInstanceArgs($args);
        } else {
            return $reflection->newInstance();
        }
    }

    /**
     * 存储实例
     * @param object $instance 实例
     */
    public static function store($instance) {
        self::$instances[get_class($instance)] = $instance;
    }

    /**
     * 检测实例是否存在
     * @param string $name 实例名称
     * @return bool
     */
    public static function isStored($name) {
        return isset(self::$instances[ltrim($name, '\\')]);
    }

    /**
     * 清除实例
     * @param string $name 实例名称
     */
    public static function clear($name) {
        $name = ltrim($name, '\\');
        if (self::isStored($name)) {
            unset(self::$instances[$name]);
        }
    }

}
