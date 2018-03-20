<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$time = microtime(true);
$memory = memory_get_usage();

require realpath('../framework/bootstrap.php');



//(new Zcan)
//        ->setDebug(true)//是否调试
//        ->setApp(realpath('../app'))//框架目录
//        ->setTimezone('PRC')//时区
//        ->setStartTime($time)//开始时间
//        ->setStartMem($memory)//开始内存
//        ->start()
//;
