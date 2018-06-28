<?php

namespace app;

use cook\router\RouterBase;


/**
 * 路由器
 */
class Router extends RouterBase {

    public function start() {
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Request-Method:GET,POST");
        header('Access-Control-Allow-Headers:Authorization,Uid,X-HTTP-Method-Override,Content-Type,x-requested-with');
        $this->app();
        $this->admin();
    }

    private function app() {

//        $this->router
//                //检测更新
//                ->get_post('/test', function (\app\model\Users $users) {
//                    for ($index = 20; $index < 30; $index++) {
//                        $users->initSuperior($index, 168170, 'b');
//                    }
//                });

        $this->router
                //检测更新
                ->get_post('/checkupdates', controller\api\User::class, 'checkupdates')

                //登录
                ->get_post('/login', controller\api\User::class, 'login')
                //注册
                ->get_post('/register', controller\api\User::class, 'register')
                //注册
                ->get_post('/recover', controller\api\User::class, 'recover')
                //获取重置验证码
                ->get_post('/getrecovercode', controller\api\User::class, 'getRecovercode')
                //获取注册验证码
                ->get_post('/getregistercode', controller\api\User::class, 'getMobileCode')
                //返回手机可被注册
                ->get_post('/register/checkmobile', controller\api\User::class, 'checkMobile')
                //验证码
                ->get_post('/captcha', controller\api\User::class, 'captcha')

        ;
        //区块链
        $this->router->get_post('/blockchain/(.*)', controller\api\Blockchain::class, 'book');




        $this->router->group('/app', function () {
            $this->router
                    //获取用户信息
                    ->get_post('/getuserinfo', controller\api\App::class, 'getUserInfo')
                    //获取用户信息
                    ->get_post('/getmoney', controller\api\App::class, 'getMoney')
                    //获取交易仓
                    ->get_post('/gettransaction', controller\api\App::class, 'getTransaction')
                    //获取锁仓
                    ->get_post('/gettransactionlock', controller\api\App::class, 'getTransactionLock')
                    //获取首页
                    ->get_post('/home', controller\api\App::class, 'getHome')
                    //退出
                    ->get_post('/signout', controller\api\App::class, 'signOut')
                    //更新用户信息
                    ->get_post('/updatepersonal', controller\api\App::class, 'updatePersonal')
                    //获取手机验证码
                    ->get_post('/getmobilecode', controller\api\App::class, 'getMobileCode')
                    //获取我的推荐
                    ->get_post('/getpartner', controller\api\App::class, 'getMyPartner')
                    //更新支付密码
                    ->get_post('/transactionpassword', controller\api\App::class, 'updateTransactionpassword')
                    //更新登录密码
                    ->get_post('/loginpassword', controller\api\App::class, 'updateLoginpassword')
                    //验证码
                    ->get_post('/captcha', controller\api\App::class, 'captcha')
                    //获取更新数据验证码
                    ->get_post('/getupdatecode', controller\api\App::class, 'getUpdateCode')
                    //锁仓
                    ->get_post('/money/lock', controller\api\App::class, 'lockMoney')
                    //检测账号
                    ->get_post('/checkaccount', controller\api\App::class, 'checkAccount')
                    //转账
                    ->get_post('/toacaount', controller\api\App::class, 'toAccount')
                    //新闻
                    ->get_post('/newsbanner', controller\api\App::class, 'newsBanner')
                    ->get_post('/news', controller\api\App::class, 'news')
                    //提交卖出
                    ->get_post('/sellsubmit', controller\api\App::class, 'sellSubmit')
                    //返回市场
                    ->get_post('/getmart', controller\api\App::class, 'getMart')
                    //行情
                    ->get_post('/quotation', controller\api\App::class, 'getQuotation')
                    ->get_post('/getquotes', controller\api\App::class, 'getQuotes')
                    //转账验证码
                    ->get_post('/gettransfercode', controller\api\App::class, 'getTransferCode')
                    //挂单售出
                    ->get_post('/getsalecode', controller\api\App::class, 'getSaleCode')
                    //返回手续费
                    ->get_post('/getfees', controller\api\App::class, 'getFees')
                    //返回交易日志
                    ->get_post('/getblocklogs', controller\api\App::class, 'getBlockLogs')
                    //返回锁仓日志
                    ->get_post('/getblocklocklogs', controller\api\App::class, 'getBlockLockLogs')
                    //当前算力
                    ->get_post('/currentpower', controller\api\App::class, 'currentPower')
                    //获取用户A,B各区的总人数,总业绩;返回最小区的业绩
                    ->get_post('/getpartnertotal', controller\api\App::class, 'getPartnerTotal')
                    //返回支付信息
                    ->get_post('/getpayinfo', controller\api\App::class, 'getPayInfo')
                    //保存支付信息
                    ->get_post('/setpayinfo', controller\api\App::class, 'setPayInfo')

                    //返回实名信息
                    ->get_post('/getverifiedinfo', controller\api\App::class, 'getVerifiedInfo')
                    //保存实名信息
                    ->get_post('/setverifiedinfo', controller\api\App::class, 'setVerifiedInfo')


            ;
        });


        $this->router->get_post('/mmzz', function (model\Users $users) {
            dump($_SERVER);
        });

        $this->router->get_post('/(A.*|B.*)', function ($code) {
            header("HTTP/1.1 301 Moved Permanently");
            Header("Location:/?code={$code}");
            exit;
        });
    }

    private function admin() {
        $this->router->group('/admin', function () {
            $this->router->get_post('/', controller\admin\Admin::class, 'index')
                    //登录
                    ->get_post('/login', controller\admin\Login::class, 'index')
                    ->get_post('/automation', controller\admin\Automatic::class, 'index')

                    //新闻新闻
                    ->get_post('/news/added', controller\admin\News::class, 'added')
                    ->get_post('/news/edited', controller\admin\News::class, 'edited')
                    ->get_post('/news/addedsubmit', controller\admin\News::class, 'addedSubmit')
                    ->get_post('/news/editedsubmit', controller\admin\News::class, 'editedsubmit')
                    ->get_post('/news/del', controller\admin\News::class, 'del')
                    ->get_post('/news/batchdel', controller\admin\News::class, 'batchDel')
                    ->get_post('/news', controller\admin\News::class, 'index')
                    //上传文件
                    ->get_post('/news/upload', controller\admin\News::class, 'upload')
                    ->get_post('/adminuser', controller\admin\AdminUser::class, 'index')
                    ->get_post('/adminuser/added', controller\admin\AdminUser::class, 'added')
                    ->get_post('/adminuser/edited', controller\admin\AdminUser::class, 'edited')
                    ->get_post('/adminuser/addedsubmit', controller\admin\AdminUser::class, 'addedsubmit')
                    ->get_post('/adminuser/editedsubmit', controller\admin\AdminUser::class, 'editedsubmit')
                    ->get_post('/adminuser/del', controller\admin\AdminUser::class, 'del')
                    ->get_post('/adminuser/adminlog', controller\admin\AdminUser::class, 'adminlog')
                    ->get_post('/user', controller\admin\User::class, 'index')
                    ->get_post('/user/edited', controller\admin\User::class, 'edited')
                    ->get_post('/user/editedsubmit', controller\admin\User::class, 'editedSubmit')
                    ->get_post('/user/addedsubmit', controller\admin\User::class, 'addedSubmit')
                    ->get_post('/user/added', controller\admin\User::class, 'added')
                    ->get_post('/user/del', controller\admin\User::class, 'del')
                    ->get_post('/user/lockmoneyedited', controller\admin\User::class, 'lockMoneyEdited')
                    ->get_post('/user/rechargemoneyedited', controller\admin\User::class, 'rechargeMoneyEdited')
                    ->get_post('/admin/added', controller\admin\Admin::class, 'added')
                    ->get_post('/admin/addedSubmit', controller\admin\Admin::class, 'addedSubmit')
                    ->get_post('/admin/setting', controller\admin\Admin::class, 'setting')
                    ->get_post('/admin/settingSubmit', controller\admin\Admin::class, 'settingSubmit')
                    ->get_post('/admin/automaticlog', controller\admin\Admin::class, 'automaticlog')
                    ->get_post('/finance/forcelogs', controller\admin\Finance::class, 'forceLogs')
                    ->get_post('/finance/dellogs', controller\admin\Finance::class, 'delLogs')
                    ->get_post('/finance/rechargemoney', controller\admin\Finance::class, 'rechargeMoney')
                    ->get_post('/finance/lockmoney', controller\admin\Finance::class, 'lockMoney')
                    ->get_post('/finance', controller\admin\Finance::class, 'index')
                    ->get_post('/finance/blockfreedlogs', controller\admin\Finance::class, 'blockFreedLogs')
                    ->get_post('/finance/blocklocklogs', controller\admin\Finance::class, 'blockLockLogs')
                    ->get_post('/finance/blocklogs', controller\admin\Finance::class, 'blockLogs')
                    ->get_post('/finance/blockpushlogs', controller\admin\Finance::class, 'blockPushLogs')
                    ->get_post('/finance/blockredlogs', controller\admin\Finance::class, 'blockRedLogs')
                    ->get_post('/finance/blocksmalllogs', controller\admin\Finance::class, 'blockSmallLogs')

                    //结算
                    ->get_post('/settlement', controller\admin\Settlement::class, 'index')
                    ->get_post('/settlement/red', controller\admin\Settlement::class, 'red')
                    ->get_post('/settlement/submitred', controller\admin\Settlement::class, 'submitred')
                    ->get_post('/settlement/lock', controller\admin\Settlement::class, 'lock')
                    ->get_post('/settlement/submitlock', controller\admin\Settlement::class, 'submitlock')
                    ->get_post('/quotation', controller\admin\Quotation::class, 'index')
                    ->get_post('/quotation/currentlist', controller\admin\Quotation::class, 'currentList')
                    ->get_post('/quotation/currentadded', controller\admin\Quotation::class, 'currentAdded')
                    ->get_post('/quotation/currentdel', controller\admin\Quotation::class, 'currentDel')
                    ->get_post('/quotation/currentedited', controller\admin\Quotation::class, 'currentEdited')
                    ->get_post('/quotation/historylist', controller\admin\Quotation::class, 'historyList')
                    ->get_post('/quotation/historyadded', controller\admin\Quotation::class, 'historyAdded')
                    ->get_post('/quotation/historyedited', controller\admin\Quotation::class, 'historyEdited')
                    ->get_post('/quotation/historydel', controller\admin\Quotation::class, 'historyDel')
                    ->get_post('/quotation/quoteslist', controller\admin\Quotation::class, 'quotesList')
                    ->get_post('/quotation/quotesadded', controller\admin\Quotation::class, 'quotesAdded')
                    ->get_post('/quotation/quotesedited', controller\admin\Quotation::class, 'quotesEdited')
                    ->get_post('/quotation/quotesdel', controller\admin\Quotation::class, 'quotesDel')
                    ->get_post('/transaction/ctoc', controller\admin\Transaction::class, 'ctoc')
                    ->get_post('/transaction/test', controller\admin\Transaction::class, 'test')


            ;
        });
    }

}
