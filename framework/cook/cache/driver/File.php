<?php

namespace cook\cache\driver;

use cook\cache\Driver;
use library\Path;
use cook\core\Config;

/**
 * 文件类型缓存类
 * @author YoPHP <admin@YoPHP.org>
 */
class File extends Driver {

    /**
     * 路径处理类
     * @var Path
     */
    public $path;

    public function __construct(Path $path, Config $config) {
        parent::__construct($config);
        $this->path = $path;
    }

    public function enabled(): bool {
        return true;
    }

    public function has($name): bool {
        return is_file($this->filename($name));
    }

    public function get($name, $default = null) {
        $filename = $this->filename($name);
        if (is_file($filename)) {
            $expire = file_get_contents($filename, false, null, 8, 12);
            if ($expire !== false) {
                $expire = intval($expire);
                if (0 === $expire || time() <= $expire) {
                    $data = file_get_contents($filename, false, null, 32);
                    return $data ? unserialize($data) : $default;
                }
            }
        }
        return $default;
    }

    public function set($name, $value, $expire = null) {
        $expire = intval($expire ?: $this->configBase['expire']) + time();
        $filename = $this->filename($name);
        $dir = dirname($filename);
        return $this->path->mkDir($dir) && is_writable($dir) && file_put_contents($filename, "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . serialize($value)) ? true : false;
    }

    public function inc($name, $step = 1) {
        $value = $this->get($name);
        $value = $value ? intval($value) + intval($step) : intval($step);
        return $this->set($name, $value, 0) ? $value : false;
    }

    public function dec($name, $step = 1) {
        $value = $this->get($name);
        $value = $value ? intval($value) - intval($step) : intval($step);
        return $this->set($name, $value, 0) ? $value : false;
    }

    public function rm($name) {
        $filename = $this->filename($name);
        return !is_readable($filename) || @unlink($filename);
    }

    public function clear() {
        return $this->path->clearDir($this->getPath());
    }

    private function getPath() {
        return rtrim($this->config->get('caching.path') ?: WRITEPATH . 'caching', '/\\');
    }

    protected function filename(string $name): string {
        $name = md5($name);
        return $this->getPath() . DS . ($name[0] . $name[1] . DS . $name[2] . $name[3] . DS . $name[4] . $name[5] . DS . $name) . '.php';
    }

}
