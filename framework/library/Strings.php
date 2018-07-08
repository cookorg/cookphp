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
     * 检查字符串是否是UTF8编码
     *
     * @param string $string 字符串
     * @return Boolean
     */
    static public function isUtf8($string): bool {
        return Validate::isUtf8($string);
    }

    /**
     * 自动转换字符集 支持数组转换
     * 
     * @param string|array $string 字符
     * @param string $from 当前编码
     * @param string $to 目标编码
     */
    static public function autoCharset($string, $from = 'gbk', $to = 'utf-8') {

        if (strtolower($from) === strtolower($to) || empty($string)) {
            return $string;
        }
        if (is_string($string)) {
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
    static public function msubstr($str, $start, $length, $suffix = true, $charset = CHARSET) {
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
    static public function randString($len = 6, $type = '') {
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

}
