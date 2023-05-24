<?php
declare(strict_types = 1);

namespace BusyPHP\facade;

use BusyPHP\oauth\Driver;
use think\Facade;

/**
 * OAuth工厂类
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/5/23 21:11 OAuth.php $
 * @method static mixed getConfig(string $name = null, mixed $default = null) 获取三方登录配置
 * @method static mixed getDriverConfig(string $driver, string $name = null, mixed $default = null) 获取指定登录驱动配置
 * @method static Driver driver(string $name = null) 获取指定登录驱动
 * @method static string getSettingKey(string $name) 获取后台设置驱动配置键名
 */
class OAuth extends Facade
{
    protected static function getFacadeClass()
    {
        return \BusyPHP\OAuth::class;
    }
}