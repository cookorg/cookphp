<?php

namespace cook\core;

use ReflectionClass;
use ReflectionFunction;
use cook\core\Container as DI;
use cook\http\Input;
use cook\core\View;

/**
 * 执行函数、类
 * @author cookphp <admin@cookphp.org>
 */
class Invoke {

    /**
     * 视图模板
     * @var View
     */
    protected $view;

    /**
     * 执行一个方法
     * @param mixed $className 类名称
     * @param mixed $name 方法名称
     * @param array $parameters 传递参数
     * @return mixed
     */
    public static function method($className, $name, array $parameters = []) {
        $class = new ReflectionClass($className);
        $construct = $class->getConstructor();
        if ($construct) {
            $args = [];
            foreach ($construct->getParameters() as $value) {
                $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName(), $className) : Input::param($value->getName());
            }
            $object = $class->newInstanceArgs($args);
        } else {
            $object = $class->newInstance();
        }
        $method = $class->getmethod($name);
        if ($method->isPublic() && !$method->isStatic()) {
            if ($method->getNumberOfParameters() > 0) {
                $args = [];
                foreach ($method->getParameters() as $value) {
                    $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName(), $method->class) : (array_shift($parameters) ?: ($value->isDefaultValueAvailable() ? $value->getDefaultValue() : Input::param($value->getName())));
                }
                $method->invokeArgs($object, $args);
            } else {
                $method->invoke($object);
            }
        }
        $view = DI::get(View::class);
        !empty($view->isTemplate()) ? $view->assign($object->data ?? null)->display() : $view->displayJson($object->data ?? null);
    }

    /**
     * 执行一个函数
     * @param mixed $name 匿名函数或函数名称
     * @param mixed $parameters 传递参数
     * @return mixed
     */
    public static function call($name, $parameters = null) {
        $function = new ReflectionFunction($name);
        if ($function->getNumberOfParameters() > 0) {
            $args = [];
            foreach ($function->getParameters() as $value) {
                $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName()) : (array_shift($parameters) ?: ($value->isDefaultValueAvailable() ? $value->getDefaultValue() : Input::param($value->getName())));
            }
            return $function->invokeArgs($args);
        } else {
            return $function->invoke();
        }
    }

}
