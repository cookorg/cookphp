<?php

namespace cook\http;

/**
 * 输入类
 * @author cookphp <admin@cookphp.org>
 */
class Input {

    /**
     * 客户请求类
     * @var Request
     */
    protected $request;
    private $input;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->input = file_get_contents('php://input');
    }

    /**
     * 获取GET
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function get(string $key = null, string $filter = null) {
        return is_null($key) ? $this->varFilter($_GET, $filter) : $this->varFilter($_GET[$key] ?? null, $filter);
    }

    /**
     * 获取POST
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function post(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || ($_input = $this->request->isContentType('json') ? json_decode($this->input, true) : $_POST);
        return is_null($key) ? $this->varFilter($_input, $filter) : $this->varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取PUT
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function put(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || parse_str($this->input, $_input);
        return is_null($key) ? $this->varFilter($_input, $filter) : $this->varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取Ddlete
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function delete(string $key = null, string $filter = null) {
        static $_input = null;
        !empty($_input) || parse_str($this->input, $_input);
        return is_null($key) ? $this->varFilter($_input, $filter) : $this->varFilter($_input[$key] ?? null, $filter);
    }

    /**
     * 获取COOKIE
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function cookie(string $key = null, string $filter = null) {
        return is_null($key) ? $this->varFilter($_COOKIE, $filter) : $this->varFilter($_COOKIE[$key] ?? null, $filter);
    }

    /**
     * 获取REQUEST
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function request(string $key = null, string $filter = null) {
        return is_null($key) ? $this->varFilter($_REQUEST, $filter) : $this->varFilter($_REQUEST[$key] ?? null, $filter);
    }

    /**
     * 获取SERVER
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function server(string $key = null, string $filter = null) {
        return is_null($key) ? $this->varFilter($_SERVER, $filter) : $this->varFilter($_SERVER[$key] ?? null, $filter);
    }

    /**
     * 获取ENV
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @param string $filter 安全过滤方法
     * @return mixed
     */
    public function env(string $key = null, string $filter = null) {
        return is_null($key) ? $this->varFilter($_ENV, $filter) : $this->varFilter($_ENV[$key] ?? null, $filter);
    }

    /**
     * 获取上传文件
     * @access public
     * @param string|null $key 名称 为空时返回所有
     * @return mixed
     */
    public function files(string $key = null) {
        return is_null($key) ? $_FILES : ($_FILES[$key] ?? null);
    }

    /**
     * 参数过滤方法
     * @access public
     * @param string|array $content 过滤内容
     * @param string $filter 过滤方法
     * @return mixed
     */
    public function varFilter($content, $filter = 'htmlspecialchars') {
        if (empty($content)) {
            return $content;
        }
        return is_array($content) ? array_map(function ($a) use ($filter ) {
                    return $this->varFilter($a, $filter);
                }, $content) : trim($filter ? $filter($content) : $content);
    }

    /**
     * 当前请求的参数
     * @access public
     * @param string $name 变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function param($name = '', $default = null) {
        $vars = $this->request->isPost() ? $this->post() : ($this->request->isPut() ? $this->put() : ($this->request->isDelete() ? $this->delete() : []));
        $param = $this->get() ? array_merge($this->get(), $vars) : $vars;
        return $name ? ($param[$name] ?? $default) : ($param ?: $default);
    }

    /**
     * 返回客户端IP
     * @access public
     * @return string
     */
    public function ip(): string {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }

}
