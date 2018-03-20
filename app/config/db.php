<?php

/**
 * 数据库
 */
return[
    //是否读写分离
    //为false是读取服务器做为主服务器
    'separate' => true,
    //表前缀
    'dbprefix' => 'c_',
    //字段标识
    'identifier' => '``',
    //具体驱动的连接选项
    'options' => [],
    //记录日志
    'logging'=>true,
    //读取服务器
    'read' => [
        [
            //数据源名称
            'dsn' => 'mysql:host=192.168.0.101;port=3306;dbname=cookphp;charset=utf8mb4',
            //账号
            'username' => 'root',
            //密码
            'password' => '123',
        ]
    ],
    //写入服务器
    'write' => [
        [
            //数据源名称
            'dsn' => 'mysql:host=192.168.0.101;port=3306;dbname=cookphp;charset=utf8mb4',
            //账号
            'username' => 'root',
            //密码
            'password' => '123'
        ]
    ],
];
