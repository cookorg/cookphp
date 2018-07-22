<?php

namespace library;

/**
 * 字符处理类
 * @author CookPHP <admin@cookphp.org>
 */
class Strings {

    /**
     * PHP替换标签字符
     * @access public
     * @param string $string 内容
     * @param string $replacer 替换标签
     * @return string
     */
    public static function parser($string, $replacer): string {
        return str_replace(array_keys($replacer), array_values($replacer), $string);
    }

    /**
     * 自动转换字符集 支持数组转换
     * 
     * @param string|array $string 字符
     * @param string $from 当前编码
     * @param string $to 目标编码
     */
    public static function autoCharset($string, $from = 'gbk', $to = 'utf-8') {
        if (strtolower($from) === strtolower($to) || empty($string)) {
            return $string;
        } elseif (is_string($string)) {
            if (function_exists('mb_convert_encoding')) {
                return mb_convert_encoding($string, $to, $from);
            } elseif (function_exists('iconv')) {
                return iconv($from, $to, $string);
            } else {
                return $string;
            }
        } elseif (is_array($string)) {
            foreach ($string as $key => $val) {
                $_key = self::autoCharset($key, $from, $to);
                $string[$_key] = self::autoCharset($val, $from, $to);
                if ($key != $_key) {
                    unset($string[$key]);
                }
            }
            return $string;
        } else {
            return $string;
        }
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param string $start 开始位置
     * @param string $length 截取长度
     * @param string $suffix 截断显示字符
     * @param string $charset 编码格式
     * @return string
     */
    public static function msubstr($str, $start, $length, $suffix = true, $charset = 'utf-8') {
        $re = [];
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = implode('', array_slice($match[0], $start, $length));
        return $suffix && $slice != $str ? $slice . '...' : $slice;
    }

    /**
     * 产生随机字串
     * @param string $len 长度
     * @param string $type 字串类型 0 字母 1 数字 其它 混合
     * @return string
     */
    public static function randString($len = 6, $type = '') {
        switch ($type) {
            case 0 :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 1 :
                $chars = str_repeat('012356789', 3);
                break;
            case 2 :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 3 :
                $chars = 'abcdefghijklmnopqrstuvwxyz';
                break;
            default :
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz2356789';
                break;
        }
        if ($len > 9) {
            $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
        }
        $chars = str_shuffle($chars);
        return substr($chars, 0, $len);
    }

    public static function sbuildOrderNo($len = 16) {
        return date('YmdHi') . substr(implode(null, array_map('ord', str_split(uniqid(), 1))), 0, $len - 12);
    }

    /**
     * 去掉UTF-8 Bom头
     * @param  string    $string
     * @access public
     * @return string
     */
    public static function removeUTF8Bom($string) {
        if (substr($string, 0, 3) == pack('CCC', 239, 187, 191)) {
            return substr($string, 3);
        }
        return $string;
    }

    /**
     * 加密和解密
     * @param string $string
     * @param bool $operation true 解密 fasle 加密
     * @param string $key
     * @param int $expiry
     * @return string
     */
    public static function authcode($string, $operation = true, $key = '', $expiry = 0) {
        $ckey_length = 4;

        if ($operation) {
            $string = str_replace(['_', '-'], ['/', '+'], $string);
        }

        $key = md5($key ? $key : 'www.cookphp.org');
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation) {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return str_replace(['/', '+'], ['_', '-'], $keyc . str_replace('=', '', base64_encode($result)));
        }
    }

}
