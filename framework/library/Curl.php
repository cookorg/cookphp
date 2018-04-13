<?php

namespace library;

/**
 * Curl类
 * @author cookphp <admin@cookphp.org>
 */
class Curl {

    /**
     * @var string
     * */
    public $cookie_file;

    /**
     * @var boolean
     * */
    public $follow_redirects = true;

    /**
     * @var array
     * */
    public $headers = [];

    /**
     * @var array
     * */
    public $options = [];

    /**
     * @var string
     * */
    public $referer;

    /**
     * @var string
     * */
    public $user_agent;

    /**
     * @var string
     * */
    protected $error = '';

    /**
     * @var resource
     * */
    protected $request;

    /**
     * @var string
     * */
    public $body = '';

    function __construct() {
        $this->cookie_file = WRITEPATH . 'curl_cookie.txt';
        $this->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Curl/PHP ' . PHP_VERSION . ' (http://www.cookphp.org)';
    }

    /**
     * HTTP DELETE 请求
     * @param string $url
     * @param array|string $vars 
     * @return CurlResponse object
     * */
    public function delete($url, $vars = []) {
        return $this->request('DELETE', $url, $vars);
    }

    /**
     * 返回当前请求的错误字符串
     * @return string
     * */
    public function error() {
        return $this->error;
    }

    /**
     * HTTP GET 请求
     * @param string $url
     * @param array|string $vars 
     * @return CurlResponse
     * */
    public function get($url, $vars = []) {
        if (!empty($vars)) {
            $url .= (stripos($url, '?') !== false) ? '&' : '?';
            $url .= (is_string($vars)) ? $vars : http_build_query($vars, '', '&');
        }
        return $this->request('GET', $url);
    }

    /**
     * HTTP HEAD 请求
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse
     * */
    public function head($url, $vars = []) {
        return $this->request('HEAD', $url, $vars);
    }

    /**
     * HTTP POST 请求
     * @param string $url
     * @param array|string $vars 
     * @return CurlResponse|boolean
     * */
    public function post($url, $vars = []) {
        return $this->request('POST', $url, $vars);
    }

    /**
     * HTTP PUT 请求
     * @param string $url
     * @param array|string $vars 
     * @return CurlResponse|boolean
     * */
    public function put($url, $vars = []) {
        return $this->request('PUT', $url, $vars);
    }

    /**
     * HTTP request 请求
     * @param string $method
     * @param string $url
     * @param array|string $vars
     * @return CurlResponse|boolean
     * */
    public function request($method, $url, $vars = []) {
        $this->error = '';
        $this->request = curl_init();
        if (is_array($vars))
            $vars = http_build_query($vars, '', '&');

        $this->set_request_method($method);
        $this->set_request_options($url, $vars);
        $this->set_request_headers();

        $response = curl_exec($this->request);

        if ($response) {
            $response = $this->response($response);
        } else {
            $this->error = curl_errno($this->request) . ' - ' . curl_error($this->request);
        }

        curl_close($this->request);

        return $response;
    }

    public function response($response) {
        # Headers regex
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';

        # Extract headers from response
        preg_match_all($pattern, $response, $matches);
        $headers_string = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));

        # Remove headers from the response body
        $this->body = str_replace($headers_string, '', $response);

        # Extract the version and status from the first header
        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->headers['Http-Version'] = $matches[1];
        $this->headers['Status-Code'] = $matches[2];
        $this->headers['Status'] = $matches[2] . ' ' . $matches[3];

        # Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
        return $this->body;
    }

    public function set_headers($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 格式化并为当前请求的headers
     * @return void
     * @access protected
     * */
    protected function set_request_headers() {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }
        curl_setopt($this->request, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * 为请求方法设置关联的CURL选项
     * @param string $method
     * @return void
     * @access protected
     * */
    protected function set_request_method($method) {
        switch (strtoupper($method)) {
            case 'HEAD':
                curl_setopt($this->request, CURLOPT_NOBODY, true);
                break;
            case 'GET':
                curl_setopt($this->request, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($this->request, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($this->request, CURLOPT_CUSTOMREQUEST, $method);
        }
    }

    /**
     * 设置当前请求的CURLOPT选项
     * @param string $url
     * @param string $vars
     * @return void
     * @access protected
     * */
    protected function set_request_options($url, $vars) {
        curl_setopt($this->request, CURLOPT_URL, $url);
        if (!empty($vars))
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $vars);

        # Set some default CURL options
        curl_setopt($this->request, CURLOPT_HEADER, true);
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->request, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookie_file) {
            curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookie_file);
            curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookie_file);
        }
        if ($this->follow_redirects)
            curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);
        if ($this->referer)
            curl_setopt($this->request, CURLOPT_REFERER, $this->referer);

        # Set any custom CURL options
        foreach ($this->options as $option => $value) {
            curl_setopt($this->request, constant('CURLOPT_' . str_replace('CURLOPT_', '', strtoupper($option))), $value);
        }
    }

}
