<?php

namespace cook\http;

use cook\http\StatusCode;
use cook\core\App;

/**
 * 输出类
 * @author cookphp <admin@cookphp.org>
 */
class Output implements StatusCode {

    /**
     * 输出内容
     * @var string
     */
    private $output;

    /**
     * header
     * @var array
     */
    public $headers = [];

    /**
     * App
     * @var App
     */
    public $app;

    public function __construct(App $app) {
        $this->app = $app;
    }

    /**
     * 输出为JSON字符
     * @param mixed $value
     * @return $this
     */
    public function setJson($value) {
        $this->output = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->setContentType('json');
        return $this;
    }

    /**
     * 获取输出
     * @return	string
     */
    public function getOutput() {
        return $this->output;
    }

    /**
     * 设置输出
     * @param	string	$output
     * @return	$this
     */
    public function setOutput($output) {
        $this->output = $output;
        return $this;
    }

    /**
     * 附加输出
     * @param	string	$output
     * @return	$this
     */
    public function appendOutput($output) {
        $this->output .= $output;
        return $this;
    }

    /**
     * 设置Header
     * @param	string	$header
     * @return	$this
     */
    public function setHeader($header) {
        $this->headers[] = $header;
        return $this;
    }

    /**
     * 获取Header
     * @return	array
     */
    public function getHeader() {
        return $this->headers;
    }

    /**
     * 设置Header Content-Type
     * @param	string	$mimeType
     * @param	string	$charset
     * @return	$this
     */
    public function setContentType($mimeType, $charset = 'UTF-8') {
        return $this->setHeader('Content-Type: ' . (is_array(($mimeType = $this->app->config->mimes[$mimeType] ?? $this->app->config->mimes['html'])) ? current($mimeType) : $mimeType) . (empty($charset) ? '' : '; charset=' . $charset));
    }

    /**
     * 获取Content-Type Header
     * @return string
     */
    public function getContentType() {
        for ($i = 0, $c = count($this->headers); $i < $c; $i++) {
            if (sscanf($this->headers[$i], 'Content-Type: %[^;]', $contentType) === 1) {
                return $contentType;
            }
        }
        return 'text/html';
    }

    /**
     * 设置Header状态
     * @param int $code
     * @param string $text
     * @return	$this
     */
    public function setStatusHeader($code = 200, $text = '') {
        if (!$this->app->request->isCli()) {
            $code = intval($code);
            !empty($text) || ($text = self::HTTP_CODE_TEXT[$code] ?? self::HTTP_CODE_TEXT[self::STATUS_INTERNAL_SERVER_ERROR]);
            $this->app->request->isCgi() ? header('Status: ' . $code . ' ' . $text, true) : header(($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . ' ' . $code . ' ' . $text, true, $code);
        }
        return $this;
    }

    /**
     * 显示输出
     * @param	string	$output
     * @return	void
     */
    public function display($output = '') {
        !empty($output) || ($output = $this->output);
        $output = str_replace(['{elapsed_time}', '{memory_usage}'], [$this->app->benchmark->elapsedTime('start_time'), $this->app->benchmark->elapsedMemory('start_memory')], $output);
        foreach ($this->headers as $header) {
            header($header, true);
        }
//        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false) {
//            $output = gzencode($output, 9);
//            header('Content-Encoding: gzip');
//            header('Vary: Accept-Encoding');
//            header('Content-Length:' . strlen($output));
//        }
        echo $output;
    }

}
