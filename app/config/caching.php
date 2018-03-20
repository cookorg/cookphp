<?php

/**
 * 缓存
 */
return[
    'driver' => 'File',
    'prefix' => md5(APPPATH),
    'expire' => 3600
];
