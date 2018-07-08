<?php

namespace library;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * 路径处理类
 * @author cookphp <admin@cookphp.org>
 */
class Path {

    /**
     * 替换目录分隔符
     * @param string $path
     * @return string
     */
    public static function replace($path) {
        return str_replace(['/', '\\'], DS, $path);
    }

    /**
     * 判断给定的文件名是否可写
     * @param string $filename 文件名称
     * @return bool
     */
    public static function isWritable($filename) {
        return is_writable($filename);
    }

    /**
     * 递归的创建目录
     * @param string $path 目录路径
     * @param int $permissions 权限
     * @return bool
     */
    public static function mkDir($path, $permissions = 0777) {
        if (is_dir($path)) {
            return true;
        }
        $_path = dirname($path);
        if ($_path !== $path) {
            self::mkDir($_path, $permissions);
        }
        return mkdir($path, $permissions);
    }

    /**
     * 清除目录
     * @param string $dir 目录
     * @return bool
     */
    public static function clearDir($dir) {
        $path = rtrim($dir, '/\\');
        if (is_dir($path)) {
            $directory = new RecursiveDirectoryIterator($path);
            $iterator = new RecursiveIteratorIterator($directory);
            $files = [];
            foreach ($iterator as $info) {
                if ($info->isFile() && $info->isWritable()) {
                    unlink($info->getPathname());
                } elseif ($info->isDir() && $info->isWritable()) {
                    $files[] = $info->getPath();
                }
            }
            if (!empty($files)) {
                $files = array_unique($files);
                foreach ($files as $value) {
                    $value != $path && rmdir($value);
                }
            }
        }
        return true;
    }

}
