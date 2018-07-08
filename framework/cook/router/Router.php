<?php

namespace cook\router;

use cook\http\RequestMethod;
use cook\http\Request;
use Exception;

/**
 * 路由器
 * @author cookphp <admin@cookphp.org>
 */
class Router implements RequestMethod {

    /**
     * @var array
     */
    protected static $routes = [];

    /**
     * @var array
     */
    public static $route = [];

    /**
     * @var string
     */
    protected static $baseRoute = '';

    /**
     * 注册HEAD路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function head(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_HEAD], $url, $controller, $action);
    }

    /**
     * 注册GET路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function get(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_GET], $url, $controller, $action);
    }

    /**
     * 注册POST路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function post(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_POST], $url, $controller, $action);
    }

    /**
     * 注册GET|POST路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function get_post(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_GET, self::METHOD_POST], $url, $controller, $action);
    }

    /**
     * 注册PUT路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function put(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_PUT], $url, $controller, $action);
    }

    /**
     * 注册PATCH路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function patch(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_PATCH], $url, $controller, $action);
    }

    /**
     * 注册DELETE路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function delete(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_DELETE], $url, $controller, $action);
    }

    /**
     * 注册PURGE路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function purge(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_PURGE], $url, $controller, $action);
    }

    /**
     * 注册OPTIONS路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function options(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_OPTIONS], $url, $controller, $action);
    }

    /**
     * 注册TRACE路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function trace(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_TRACE], $url, $controller, $action);
    }

    /**
     * 注册CONNECT路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function connect(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_CONNECT], $url, $controller, $action);
    }

    /**
     * 注册到所有路由
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * 
     */
    public static function any(string $url, $controller, string $action = '') {
        return self::map([self::METHOD_HEAD, self::METHOD_GET, self::METHOD_POST, self::METHOD_PUT, self::METHOD_PATCH, self::METHOD_DELETE, self::METHOD_PURGE, self::METHOD_OPTIONS, self::METHOD_TRACE, self::METHOD_CONNECT], $url, $controller, $action);
    }

    /**
     * 注册路由组
     * @param string $baseRoute
     * @param callable $callback
     * @param array|string $methods
     * @return void
     */
    public static function group($baseRoute, $callback) {
        self::$baseRoute = '';
        if (is_callable($callback)) {
            $curBaseRoute = self::$baseRoute;
            self::$baseRoute .= $baseRoute;
            call_user_func($callback);
            self::$baseRoute = $curBaseRoute;
        }
    }

    /**
     * 注册到路由
     * @param array $methods HTTP方法名称
     * @param string $url URL或正则表达
     * @param string|callable $controller 控制器或回调函数
     * @param string $action 方法
     * @param string $template 模板
     */
    public static function map(array $methods, string $url, $controller, string $action = '') {
        return self::addRoute(['methods' => $methods, 'url' => self::$baseRoute ? rtrim(self::$baseRoute . '/' . trim($url, '/'), '/') : $url, 'controller' => $controller, 'action' => $action]);
    }

    /**
     * 注册路由
     * @param array $data
     */
    public static function addRoute(array $data) {
        $route = [
            'methods' => $data['methods'] ?? [self::METHOD_GET],
            'url' => $data['url'] ?? null,
            'controller' => $data['controller'] ?? null,
            'action' => $data['action'] ?? null
        ];

        if (!$route['controller']) {
            throw new Exception('需要控制器和方法组合或可调用函数');
        }
        if (empty($route['methods']) || !is_array($route['methods'])) {
            throw new Exception('必须指定一个请求模式');
        }
        self::$routes[$route['url']] = $route;
    }

    /**
     * 匹配路由
     * @param string $url
     * 
     */
    public static function matchUrl($url = null) {
        return (self::matchUrlDirectly(($url = '/' . ltrim($url ?: Request::getPathInfo(), '/'))) || self::matchUrlWithParameters($url)) ? true : null;
    }

    /**
     * 尝试直接匹配路由线路
     * @param string $url
     * @return Route|null
     */
    protected static function matchUrlDirectly($url) {
        if (array_key_exists($url, self::$routes) && self::isAcceptedRequestMethod(self::$routes[$url]['methods'])) {
            self::$route = self::$routes[$url];
            return true;
        }
        return null;
    }

    /**
     * 尝试匹配路由线路参数
     * @param string $url
     * @return Route|null
     */
    protected static function matchUrlWithParameters($url) {
        foreach (self::$routes as $route) {
            if (preg_match_all('/^' . str_replace('/', '\/', $route['url']) . '$/', $url, $match) && self::isAcceptedRequestMethod($route['methods'])) {
                self::$route = $route += ['values' => $match[1] ?? []];
                return true;
            }
        }
        return null;
    }

    /**
     * 检测请求方法是否正确
     * @param string $methods
     * @return bool
     */
    protected static function isAcceptedRequestMethod($methods) {
        return in_array(Request::getMethod(), $methods);
    }

    /**
     * 获取所有值
     * @return array
     */
    public static function getValues() {
        return self::$route['values'] ?? [];
    }

}
