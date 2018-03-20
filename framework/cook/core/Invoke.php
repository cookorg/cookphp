<?php

namespace cook\core;

use ReflectionClass;
use ReflectionFunction;
use cook\core\Container as DI;
use cook\http\Input;

/**
 * 执行函数、类
 * @author cookphp <admin@cookphp.org>
 */
class Invoke {

    /**
     * 客户输入类
     * @var Input
     */
    protected $input;

    public function __construct(Input $input) {
        $this->input = $input;
    }

    /**
     * 执行一个方法
     * @param mixed $className 类名称
     * @param mixed $name 方法名称
     * @param array $parameters 传递参数
     * @return mixed
     */
    public function method($className, $name, array $parameters = []) {
        $class = new ReflectionClass($className);
        $construct = $class->getConstructor();
        if ($construct) {
            $args = [];
            foreach ($construct->getParameters() as $value) {
                $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName(), $className) : $this->input->param($value->getName());
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
                    $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName(), $method->class) : (array_shift($parameters) ?: ($value->isDefaultValueAvailable() ? $value->getDefaultValue() : $this->input->param($value->getName())));
                }
                return $method->invokeArgs($object, $args);
            } else {
                return $method->invoke($object);
            }
        }
    }

    /**
     * 执行一个函数
     * @param mixed $name 匿名函数或函数名称
     * @param mixed $parameters 传递参数
     * @return mixed
     */
    public function call($name, $parameters = null) {
        $function = new ReflectionFunction($name);
        if ($function->getNumberOfParameters() > 0) {
            $args = [];
            foreach ($function->getParameters() as $value) {
                $args[] = is_object($value->getClass()) ? DI::get($value->getClass()->getName()) : ($parameters ?: ($value->isDefaultValueAvailable() ? $value->getDefaultValue() : $this->input->param($value->getName())));
            }
            return $function->invokeArgs($args);
        } else {
            return $function->invoke();
        }
    }

}
