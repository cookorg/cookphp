<?php


namespace app\plugin;

/**
 * 生成多层树状下拉选框的工具模型
 */
class Tree  {

    /**
     * 把返回的数据集转换成Tree
     *
     * @access public
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    public function toTree($list = null, $pk = 'id', $pid = 'pid', $child = '_child') {
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

    /**
     * 将格式数组转换为树
     *
     * @param array $list
     * @param integer $level 进行递归时传递用的参数
     */
    private $formatTree;

    private function _toFormatTree($list, $level = 0, $title = 'title') {
        foreach ($list as $key => $val) {
            $tmp_str = '└' . str_repeat("-", $level * 2);
            $tmp_str .= "-";
            $val['level'] = $level;
            $val['title_show'] = $level == 0 ? $val[$title] . "" : $tmp_str . $val[$title] . "";
            // $val['title_show'] = $val['id'].'|'.$level.'级|'.$val['title_show'];
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $title);
            }
        }
        return;
    }

    public function toFormatTree($list, $title = 'name', $pk = 'id', $pid = 'pid', $root = 0) {
        $list = $this->list_to_tree($list, $pk, $pid, '_child', $root);
        $this->formatTree = [];
        $this->_toFormatTree($list, 0, $title);
        return $this->formatTree;
    }

    private $formatSelect;

    private function formatCategorySelect($list, $id = null) {
        foreach ($list as $value) {
            if (isset($value['_child'])) {
                $this->formatSelect .= '<optgroup label="' . $value['name'] . '">';
                $this->formatCategorySelect($value['_child'], $id);
                $this->formatSelect .= '</optgroup>';
            } else {
                if ((is_array($id) && in_array($value['id'], $id)) || (is_number($id) && intval($id) === intval($value['id']))) {
                    $this->formatSelect .= '<option value="' . $value['id'] . '" selected="selected">' . $value['name'] . '</option>';
                } else {
                    $this->formatSelect .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
                }
            }
        }
        return $this->formatSelect;
    }

    public function getCategorySelect($list, $id = null) {
        $this->formatSelect = '';
        $this->formatCategorySelect($this->toTree($list), $id);
        return $this->formatSelect;
    }

    /**
     * 返回数组的维度
     */
    function arrayLevel($arr) {
        $al = 1;

        function arrayL($arr, &$al) {
            if (isset($arr[0]['_child']) && is_array($arr[0]['_child'])) {
                $al++;
                arrayL($arr[0]['_child'], $al);
            }
        }

        arrayL($arr, $al);
        return $al;
    }

    /**
     * 返回所有的叶子节点
     * @param type $result
     * @param type $fid
     * @return int|array
     */
    public function scanNodeOfTree($result, $fid) {
        $checkexist = false;
        for ($i = 0; $i < count($result); $i++) {
            if ($fid == $result[$i]['pid']) {
                $checkexist = true;
                $arr .= $this->scanNodeOfTree($result, $result[$i]['id']) . ',';
            }
        }
        if (!$checkexist) {
            return $fid;
        }
        return $arr;
    }

    /**
     * 返回所有的上级节点
     * @param type $result
     * @param type $id
     * @param type $arr
     * @return array
     */
    public function getNodeOfTree($result, $id, $arr = []) {
        if ($id == 0) {
            return $arr;
        }
        foreach ($result as $items) {
            if ($id == $items['id']) {
                $arr[] = $items;
                $return = $this->getNodeOfTree($result, $items['pid'], $arr);
            }
        }
        return $return;
    }

    /**
     * 把返回的数据集转换成Tree
     *
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = & $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = & $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = & $refer[$parentId];
                        $parent[$child][] = & $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

}
