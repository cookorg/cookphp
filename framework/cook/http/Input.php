<?php

namespace cook\http;

/**
 * 输入类
 * @author cookphp <admin@cookphp.org>
 */
class Input {

    /**
     * 获取GET
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function get(string $key = null, string $filter = null) {
        return is_null($key) ? self::varFilter($_GET, $filter) : self::varFilter($_GET[$key] ?? null, $filter);
    }

    /**
     * 获取POST
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function post(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || ($_input = Request::isContentType('json') ? json_decode(file_get_contents('php://input'), true) : $_POST);
        return is_null($key) ? self::varFilter($_input, $filter) : self::varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取PUT
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function put(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || parse_str(file_get_contents('php://input'), $_input);
        return is_null($key) ? self::varFilter($_input, $filter) : self::varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取Ddlete
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function delete(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || parse_str(file_get_contents('php://input'), $_input);
        return is_null($key) ? self::varFilter($_input, $filter) : self::varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取COOKIE
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function cookie(string $key = null, string $filter = null) {
        return is_null($key) ? self::varFilter($_COOKIE, $filter) : self::varFilter($_COOKIE[$key] ?? null, $filter);
    }

    /**
     * 获取REQUEST
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function request(string $key = null, string $filter = null) {
        return is_null($key) ? self::varFilter($_REQUEST, $filter) : self::varFilter($_REQUEST[$key] ?? null, $filter);
    }

    /**
     * 获取SERVER
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function server(string $key = null, string $filter = null) {
        return is_null($key) ? self::varFilter($_SERVER, $filter) : self::varFilter($_SERVER[$key] ?? null, $filter);
    }

    /**
     * 获取ENV
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public static function env(string $key = null, string $filter = null) {
        return is_null($key) ? self::varFilter($_ENV, $filter) : self::varFilter($_ENV[$key] ?? null, $filter);
    }

    /**
     * 获取上传文件
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @return mixed
     */
    public static function files(string $key = null) {
        return is_null($key) ? $_FILES : ($_FILES[$key] ?? null);
    }

    /**
     * 参数过滤方法
     * @access public
     * @param string|array $content 过滤内容
     * @param string $filter 过滤方法
     * @return mixed
     */
    public static function varFilter($content, $filter = 'htmlspecialchars') {
        if (empty($content)) {
            return $content;
        }
        return is_array($content) ? array_map(function ($a) use ($filter ) {
                    return self::varFilter($a, $filter);
                }, $content) : trim($filter ? $filter($content) : $content);
    }

    /**
     * 当前请求的参数
     * @access public
     * @param string $name 变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function param($name = '', $default = null) {
        $vars = Request::isPost() ? self::post() : (Request::isPut() ? self::put() : (Request::isDelete() ? self::delete() : []));
        $param = self::get() ? array_merge(self::get(), $vars) : $vars;
        return $name ? ($param[$name] ?? $default) : ($param ?: $default);
    }

    /**
     * 返回客户端IP
     * @access public
     * @return string
     */
    public static function ip() {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    }

}
