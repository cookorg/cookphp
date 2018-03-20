<?php

namespace app;

use cook\router\RouterBase;

/**
 * 路由器
 */
class Router extends RouterBase {

    public function start() {
        $this->admin();
        $this->weixin();
    }

    private function admin() {

        
        
        $this->router
                ->get_post('/', controller\Index::class, 'index')
               ;
    }

    private function weixin() {
        
    }

}
