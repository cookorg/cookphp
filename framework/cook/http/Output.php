<?php

namespace cook\http;

use cook\http\StatusCode;
use cook\core\Benchmark;
use cook\core\Config;
use cook\http\Request;

/**
 * 输出类
 * @author cookphp <admin@cookphp.org>
 */
class Output implements StatusCode {

    /**
     * 输出内容
     * @var string
     */
    private static $output;

    /**
     * header
     * @var array
     */
    public static $headers = [];

    /**
     * 输出为JSON字符
     * @param mixed $value
     */
    public static function setJson($value) {
        self::$output = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        self::setContentType('json');
    }

    /**
     * 获取输出
     * @return	string
     */
    public static function getOutput() {
        return self::$output;
    }

    /**
     * 设置输出
     * @param	string	$output
     */
    public static function setOutput($output) {
        self::$output = $output;
    }

    /**
     * 附加输出
     * @param	string	$output
     */
    public static function appendOutput($output) {
        self::$output .= $output;
    }

    /**
     * 设置Header
     * @param	string	$header
     */
    public static function setHeader($header) {
        self::$headers[] = $header;
    }

    /**
     * 获取Header
     * @return	array
     */
    public static function getHeader() {
        return self::$headers;
    }

    /**
     * 设置Header Content-Type
     * @param	string	$mimeType
     * @param	string	$charset
     */
    public static function setContentType($mimeType, $charset = 'UTF-8') {
        return self::setHeader('Content-Type: ' . (is_array(($mimeType = Config::get('mimes.' . $mimeType) ?: Config::get('mimes.html'))) ? current($mimeType) : $mimeType) . (empty($charset) ? '' : '; charset=' . $charset));
    }

    /**
     * 获取Content-Type Header
     * @return string
     */
    public static function getContentType() {
        for ($i = 0, $c = count(self::$headers); $i < $c; $i++) {
            if (sscanf(self::$headers[$i], 'Content-Type: %[^;]', $contentType) === 1) {
                return $contentType;
            }
        }
        return 'text/html';
    }

    /**
     * 设置Header状态
     * @param int $code
     * @param string $text
     */
    public static function setStatusHeader($code = 200, $text = '') {
        if (!Request::isCli()) {
            $code = intval($code);
            !empty($text) || ($text = self::HTTP_CODE_TEXT[$code] ?? self::HTTP_CODE_TEXT[self::STATUS_INTERNAL_SERVER_ERROR]);
            Request::isCgi() ? header('Status: ' . $code . ' ' . $text, true) : header(($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . ' ' . $code . ' ' . $text, true, $code);
        }
    }

    /**
     * 显示输出
     * @param	string	$output
     * @return	void
     */
    public static function display($output = '') {
        !empty($output) || ($output = self::$output);
        $output = str_replace(['{elapsed_time}', '{memory_usage}'], [Benchmark::elapsedTime('start_time'), Benchmark::elapsedMemory('start_memory')], $output);
        foreach (self::$headers as $header) {
            header($header, true);
        }
        if (!DEBUG && strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false) {
            $output = gzencode($output, 9);
            header('Content-Encoding: gzip');
            header('Vary: Accept-Encoding');
            header('Content-Length:' . strlen($output));
            header('X-Powered-By:CookPHP');
        }
        echo $output;
    }

}
