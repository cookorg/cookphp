<?php

namespace library;

/**
 * 树形数组
 */
class Tree {

    /**
     * 把返回的数据集转换成Tree
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    public static function toTree($list = null, $pk = 'id', $pid = 'pid', $child = '_child') {
        if (null === $list)
            return [];
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                $_key = is_object($data) ? $data->$pk : $data[$pk];
                $refer[$_key] = & $list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = is_object($data) ? $data->$pid : $data[$pid];
                $is_exist_pid = false;
                foreach ($refer as $k => $v) {
                    if ($parentId == $k) {
                        $is_exist_pid = true;
                        break;
                    }
                }
                if ($is_exist_pid) {
                    if (isset($refer[$parentId])) {
                        $parent = & $refer[$parentId];
                        $parent[$child][] = & $list[$key];
                    }
                } else {
                    $tree[] = & $list[$key];
                }
            }
        }
        return $tree;
    }

}
