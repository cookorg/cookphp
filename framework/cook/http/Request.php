<?php

namespace cook\http;

use cook\http\RequestMethod;

/**
 * 客户请求类
 * @author cookphp <admin@cookphp.org>
 */
class Request implements RequestMethod {

    /**
     * @return string
     */
    public static function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public static function isMethod($method) {
        return self::getMethod() === $method;
    }

    /**
     * 是否为HEAD请求
     * @return bool
     */
    public static function isHead() {
        return self::isMethod(self::METHOD_HEAD);
    }

    /**
     * 是否为GET请求
     * @return bool
     */
    public static function isGet() {
        return self::isMethod(self::METHOD_GET);
    }

    /**
     * 是否为POST请求
     * @return bool
     */
    public static function isPost() {
        return self::isMethod(self::METHOD_POST);
    }

    /**
     * 是否为PUT请求
     * @return bool
     */
    public static function isPut() {
        return self::isMethod(self::METHOD_PUT);
    }

    /**
     * 是否为PATCH请求
     * @return bool
     */
    public static function isPatch() {
        return self::isMethod(self::METHOD_PATCH);
    }

    /**
     * 是否为DELETE请求
     * @return bool
     */
    public static function isDelete() {
        return self::isMethod(self::METHOD_DELETE);
    }

    /**
     * 是否为PURGE请求
     * @return bool
     */
    public static function isPurge() {
        return self::isMethod(self::METHOD_PURGE);
    }

    /**
     * 是否为OPTIONS请求
     * @return bool
     */
    public static function isOptions() {
        return self::isMethod(self::METHOD_OPTIONS);
    }

    /**
     * 是否为TRACE请求
     * @return bool
     */
    public static function isTrace() {
        return self::isMethod(self::METHOD_TRACE);
    }

    /**
     * 是否为CONNECT请求
     * @return bool
     */
    public static function isConnect() {
        return self::isMethod(self::METHOD_CONNECT);
    }

    /**
     * 是否为cli
     * @return bool
     */
    public static function isCli() {
        return PHP_SAPI == 'cli' ? true : false;
    }

    /**
     * 是否为cgi
     * @return bool
     */
    public static function isCgi() {
        return strpos(PHP_SAPI, 'cgi') === 0;
    }

    /**
     * 当前是否Ajax请求
     * @return bool
     */
    public static function isAjax() {
        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') ? true : false;
    }

    /**
     * 当前是否Pjax请求
     * @return bool
     */
    public static function isPjax() {
        return isset($_SERVER['HTTP_X_PJAX']) ? true : false;
    }

    /**
     * 检测当前请求类型
     * @param string $type
     * @return bool
     */
    public static function isContentType($type) {
        return strpos(self::getContentType(), $type) !== false;
    }

    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @return string
     */
    public static function getContentType() {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }

    /**
     * 返回路由请求路径
     * @return string
     */
    public static function getPathInfo() {
        return trim(self::isCli() ? ($_SERVER['argv'][1] ?? '') : ($_SERVER['PATH_INFO'] ?? ''), '/');
    }

}
