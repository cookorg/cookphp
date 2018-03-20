<?php

use cook\core\Container as DI;
use cook\core\Autoloader;
use cook\core\App;

defined('DEBUG') ||
        /**
         * 是否调试模式
         * @var bool
         */
        define('DEBUG', false);

defined('DS') ||
        /**
         * 简化路径的分隔符
         * @var string
         */
        define('DS', DIRECTORY_SEPARATOR);

defined('ROOTPATH') ||
        /**
         * 主程序路径
         * @var string
         */
        define('ROOTPATH', dirname(__DIR__) . DS);

defined('BASEPATH') ||
        /**
         * 框架路径
         * @var string
         */
        define('BASEPATH', ROOTPATH . 'framework' . DS);

defined('PUBLICATH') ||
        /**
         * 公众路径
         * @var string
         */
        define('PUBLICATH', ROOTPATH . 'public' . DS);

defined('APPPATH') ||
        /**
         * 项目路径
         * @var string
         */
        define('APPPATH', ROOTPATH . 'app' . DS);

defined('CONFIGPATH') ||
        /**
         * 配制文件路径
         * @var string
         */
        define('CONFIGPATH', APPPATH . 'config' . DS);

defined('WRITEPATH') ||
        /**
         * 可写目录的路径
         * @var string
         */
        define('WRITEPATH', ROOTPATH . 'writable' . DS);

defined('APPNAMESPACE') ||
        /**
         * APP命名空间名称
         * @var string
         */
        define('APPNAMESPACE', 'App');

require BASEPATH . 'cook' . DS . 'core' . DS . 'Container.php';
require BASEPATH . 'cook' . DS . 'core' . DS . 'Autoloader.php';

DI::get(Autoloader::class)->register(CONFIGPATH . 'autoloader.php', BASEPATH . 'autoloader.php');
DI::get(App::class)->run();
