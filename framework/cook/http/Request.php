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
    public function getMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? self::METHOD_GET);
    }

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method) {
        return $this->getMethod() === $method;
    }

    /**
     * 是否为HEAD请求
     * @return bool
     */
    public function isHead() {
        return $this->isMethod(self::METHOD_HEAD);
    }

    /**
     * 是否为GET请求
     * @return bool
     */
    public function isGet() {
        return $this->isMethod(self::METHOD_GET);
    }

    /**
     * 是否为POST请求
     * @return bool
     */
    public function isPost() {
        return $this->isMethod(self::METHOD_POST);
    }

    /**
     * 是否为PUT请求
     * @return bool
     */
    public function isPut() {
        return $this->isMethod(self::METHOD_PUT);
    }

    /**
     * 是否为PATCH请求
     * @return bool
     */
    public function isPatch() {
        return $this->isMethod(self::METHOD_PATCH);
    }

    /**
     * 是否为DELETE请求
     * @return bool
     */
    public function isDelete() {
        return $this->isMethod(self::METHOD_DELETE);
    }

    /**
     * 是否为PURGE请求
     * @return bool
     */
    public function isPurge() {
        return $this->isMethod(self::METHOD_PURGE);
    }

    /**
     * 是否为OPTIONS请求
     * @return bool
     */
    public function isOptions() {
        return $this->isMethod(self::METHOD_OPTIONS);
    }

    /**
     * 是否为TRACE请求
     * @return bool
     */
    public function isTrace() {
        return $this->isMethod(self::METHOD_TRACE);
    }

    /**
     * 是否为CONNECT请求
     * @return bool
     */
    public function isConnect() {
        return $this->isMethod(self::METHOD_CONNECT);
    }

    /**
     * 是否为cli
     * @return bool
     */
    public function isCli() {
        return PHP_SAPI == 'cli' ? true : false;
    }

    /**
     * 是否为cgi
     * @return bool
     */
    public function isCgi() {
        return strpos(PHP_SAPI, 'cgi') === 0;
    }

    /**
     * 当前是否Ajax请求
     * @return bool
     */
    public function isAjax() {
        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') ? true : false;
    }

    /**
     * 当前是否Pjax请求
     * @return bool
     */
    public function isPjax() {
        return isset($_SERVER['HTTP_X_PJAX']) ? true : false;
    }

    /**
     * 检测当前请求类型
     * @param string $type
     * @return bool
     */
    public function isContentType($type) {
        return strpos($this->getContentType(), $type) !== false;
    }

    /**
     * 当前请求 HTTP_CONTENT_TYPE
     * @return string
     */
    public function getContentType() {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }

    /**
     * 返回路由请求路径
     * @return string
     */
    public function getPathInfo() {
        return trim($this->isCli() ? ($_SERVER['argv'][1] ?? '') : ($_SERVER['PATH_INFO'] ?? ''), '/');
    }

}
