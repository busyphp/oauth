<?php
declare(strict_types = 1);

namespace BusyPHP\oauth;

use BusyPHP\app\admin\model\system\menu\SystemMenu;
use BusyPHP\oauth\app\controller\SettingController;

class Service extends \think\Service
{
    public function boot() : void
    {
        $this->registerRoutes(function() {
            SystemMenu::registerAnnotation(SettingController::class);
        });
    }
}