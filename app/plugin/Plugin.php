<?php

namespace Model;

namespace app\plugin;

/**
 * 插件管理
 * @author admin@xuai.cn
 * @nav TRUE
 */
class Plugin{

    public function getPlugin($dir, $namespace) {
        return $this->initPlugin($dir, $namespace);
    }

    /**
     * 初始导航
     */
    private function initPlugin($dir, $namespace) {
        $file = [];
        $handle = opendir($dir);
        if ($handle) {
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') {
                    continue;
                }
                $file[] = '\\' . $namespace . '\\' . basename($filename, '.php');
            }
            closedir($handle);
        }


        $authority = [];
        for ($index = 0; $index < count($file); $index++) {
            $authority[] = $this->reflectionClass($file[$index]);
        }
        //$authority = Sort::arrayAsc($authority);
        $plugin = [];
        for ($index1 = 0; $index1 < count($authority); $index1++) {
            $plugin[$authority[$index1]['name']] = $authority[$index1];
            $plugin[$authority[$index1]['name']]['count'] = 0;
            unset($plugin[$authority[$index1]['name']]['plugin']);
            for ($index2 = 0; $index2 < count($authority[$index1]['plugin']); $index2++) {

                if (!empty($authority[$index1]['plugin'][$index2]['nav'])) {
                    $plugin[$authority[$index1]['name']]['count'] ++;
                }

                $plugin[$authority[$index1]['name']]['plugin'][$authority[$index1]['plugin'][$index2]['name']] = $authority[$index1]['plugin'][$index2];
            }
        }
        return $plugin;
    }

    /**
     * 分析类
     * @param string $class
     * @return array
     */
    public function reflectionClass($class) {
        $parser = new Parser();
        $reflection = new \ReflectionClass($class);
        $parser->init($reflection->getDocComment());
        $parser->parse();
        $params = $parser->getParams();
        $list = [];
        $name = explode('\\', $reflection->name);
        $list['name'] = end($name);
        $list['desc'] = $parser->getDesc() ?: $parser->getShortDesc();
        $list['plugin'] = [];
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^[A-Za-z]+$/', $method->name) && !empty($method->getDocComment())) {
                $parser->init($method->getDocComment());
                $parser->parse();
                $params = $parser->getParams();
                $list['plugin'][] = [
                    'name' => $method->name,
                    'desc' => $parser->getDesc() ?: $parser->getShortDesc()
                ];
            }
        }

        return $list;
    }

}
