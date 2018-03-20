<?php

namespace cook\core;

/**
 * 自动加载
 * @author zcan <admin@zcan.cn>
 */
class Autoloader {

    /**
     * 命名空间
     * @var array
     */
    protected $prefixes = [];

    public function __construct() {
        spl_autoload_register([$this, 'loader'], true, true);
    }

    /**
     * 自动加载
     * @param array $autoloader
     */
    public function register(...$autoloader) {
        array_map(function($autoloader) {
            if (is_string($autoloader)) {
                $autoloader = $this->requireFile($autoloader, true);
            }
            if (is_array($autoloader)) {
                foreach ($autoloader as $key => $value) {
                    $this->addNamespace($key, $value);
                }
            }
        }, $autoloader);
        return $this;
    }

    /**
     * 注册本地命名空间
     * @param string $prefix 命名空间前缀
     * @param string $baseDir 类文件的基本目录
     * @param bool $prepend true最先搜索到否则最后
     */
    public function addNamespace($prefix, $baseDir, $prepend = false) {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = str_replace(['/', '\\'], DS, rtrim($baseDir, '/\\') . DS);
        if ($baseDir !== false) {
            if (isset($this->prefixes[$prefix])) {
                $prepend ? array_unshift($this->prefixes[$prefix], $baseDir) : array_push($this->prefixes[$prefix], $baseDir);
            } else {
                $this->prefixes[$prefix][] = $baseDir;
            }
        }
        return $this;
    }

    /**
     * 获取已注册的名空间
     * @param string $prefix 命名空间前缀 null时返回所有
     * @return mixed
     */
    public function getLocalNamespace($prefix = null) {
        return $prefix === null ? $this->prefixes : $this->prefixes[$prefix] ?? null;
    }

    /**
     * 加载类
     * @param string $class 类名称
     * @return bool
     */
    public function loader($class) {
        $prefix = $class;
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);
            if ($this->loadMappedFile($prefix, $relativeClass)) {
                return true;
            } else {
                $prefix = rtrim($prefix, '/\\');
            }
        }
        return false;
    }

    /**
     * 加载命名空间前缀和相对类的映射文件
     * @param string $prefix 命名空间前缀
     * @param string $filename 相对类
     * @return bool
     */
    public function loadMappedFile($prefix, $filename) {
        if (isset($this->prefixes[$prefix])) {
            foreach ($this->prefixes[$prefix] as $baseDir) {
                if ($this->requireFile($baseDir . $filename . '.php')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 如果存在文件，则加载文件系统
     * @param string $filename 需要加载的文件
     * @param bool $return 是否返回加载文件
     * @return bool
     */
    public function requireFile($filename, $return = false) {
        $file = str_replace(['/', '\\'], DS, $filename);
        if (is_readable($file)) {
            if ($return) {
                return require $file;
            }
            require $file;
            return true;
        } else {
            return false;
        }
    }

}
