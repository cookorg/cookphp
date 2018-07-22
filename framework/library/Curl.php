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
    public $cookieFile;

    /**
     * @var boolean
     * */
    public $followRedirects = true;

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
    public $userAgent;

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

    /**
     * @var int
     * */
    public $timeout = 30;

    /**
     * @var string
     * */
    protected $sslcertPath;

    /**
     * @var string
     * */
    protected $sslkeyPath;

    /**
     * @var string
     * */
    protected $userpwd;

    /**
     * @var array
     * */
    protected $files = [];

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
     * 设置Header
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeaders($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * 设置SSL
     * @param string $sslcertPath
     * @param string $sslkeyPath
     * @return $this
     */
    public function setSSl($sslcertPath, $sslkeyPath) {
        $this->sslcertPath = $sslcertPath;
        $this->sslkeyPath = $sslkeyPath;
        return $this;
    }

    /**
     * 设置CookieFile
     * @param string $cookieFile
     * @return $this
     */
    public function setCookieFile($cookieFile) {
        $this->cookieFile = $cookieFile;
        return $this;
    }

    /**
     * 设置userAgent
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * 设置userpwd
     * @param string $userpwd
     * @return $this
     */
    public function setUserPassword($userpwd) {
        $this->userpwd = $userpwd;
        return $this;
    }

    /**
     * 设置超时
     * @param string $timeout
     * @return $this
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
        return $this;
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
//        if (is_array($vars)) {
//            $vars = http_build_query($vars, '', '&');
//        }
        $this->setRequestMethod($method);
        $this->setRequestOptions($url, $vars);
        $this->setRequestHeaders();
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
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
        preg_match_all($pattern, $response, $matches);
        $headers_string = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));
        $this->body = str_replace($headers_string, '', $response);
        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->headers['Http-Version'] = $matches[1];
        $this->headers['Status-Code'] = $matches[2];
        $this->headers['Status'] = $matches[2] . ' ' . $matches[3];
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
        return $this->body;
    }

    /**
     * 格式化并为当前请求的headers
     * @return void
     * @access protected
     * */
    protected function setRequestHeaders() {
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
    protected function setRequestMethod($method) {
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
    protected function setRequestOptions($url, $vars) {
        curl_setopt($this->request, CURLOPT_URL, $url);
        if (!empty($vars)) {
            curl_setopt($this->request, CURLOPT_POSTFIELDS, $vars);
        }
        curl_setopt($this->request, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->request, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->request, CURLOPT_HEADER, true);
        curl_setopt($this->request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->request, CURLOPT_USERAGENT, $this->userAgent ?: "CookPHP/0.1 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . curl_version()['version']);
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($this->request, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->request, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (!empty($this->sslcertPath) && !empty($this->sslkeyPath)) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->sslcertPath);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->sslkeyPath);
        }
        if ($this->cookieFile) {
            curl_setopt($this->request, CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($this->request, CURLOPT_COOKIEJAR, $this->cookieFile);
        }
        if ($this->followRedirects) {
            curl_setopt($this->request, CURLOPT_FOLLOWLOCATION, true);
        }
        if ($this->referer) {
            curl_setopt($this->request, CURLOPT_REFERER, $this->referer);
        }
        if (!empty($this->userpwd)) {
            curl_setopt($this->request, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($this->request, CURLOPT_USERPWD, $this->userpwd);
            curl_setopt($this->request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        }
        foreach ($this->options as $option => $value) {
            curl_setopt($this->request, constant('CURLOPT_' . str_replace('CURLOPT_', '', strtoupper($option))), $value);
        }
    }

}
