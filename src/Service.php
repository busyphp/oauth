<?php

namespace BusyPHP\oauth;


use think\Route;

/**
 * OAuthService
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/7 下午5:29 下午 Service.php $
 */
class Service extends \think\Service
{
    public function boot()
    {
        $this->registerRoutes(function(Route $route) {
            $route->rule('general/plugin/install/oauth', InstallController::class . '@index');
        });
    }
}