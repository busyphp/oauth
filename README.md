BusyPHP OAuth2.0 登录模块
===============

## 说明

用于BusyPHP进行三方登录的基本组件

## 安装
```
composer require busyphp/oauth
```

> 安装成功后在后台 > 开发模式 > 插件管理 进行数据表安装

> 通过后台 > 系统 > 系统管理 > 系统设置 > 三方登录 进行参数配置

## 配置 `config/oauth.php`
```php
return [
    // 登录驱动配置
    'drivers'  => [
        // '驱动别名' => [
        //     'type' => '驱动名称',
        // ]
    ]
];
```

## 接口说明

### OAuth2.0接口类

三方登录接口需要继承该接口

> `BusyPHP\oauth\Driver`

### 模型接口

用户模型需要集成该接口

> `BusyPHP\oauth\interfaces\OAuthUserModelInterface`

## 使用方法

```php
<?php
namespace app\home\controller;

use BusyPHP\Controller;
use BusyPHP\facade\OAuth;
use BusyPHP\oauth\interfaces\OAuthInfo;

class Index extends Controller
{
    public function index()
    {
        // 获取驱动
        $driver = OAuth::driver('驱动别名，由 config/oauth.php 定义');
        
        // 设置驱动登录数据，请依据不同的登录驱动提供不同的 OAuthDataInterface
        $driver->setData();
        
        // 执行普通登录
        $result = $driver->login(UserModel::init(), function(OAuthInfo $auth, UserModel $model) {
            // 执行注册校验
            // 如果已注册，请返回注册的用户ID
            // 否则返回自定义注册数据
            return [];
        });
        
        // 执行浏览器登录
        $result = $driver->webLogin('回跳地址', UserModel::init(), function(OAuthInfo $auth, UserModel $model) {
            // 执行注册校验
            // 如果已注册，请返回注册的用户ID
            // 否则返回自定义注册数据
            return [];
        });
        
        // 登录成功返回的 UserModel 数据
        $result->userInfo;
        
        // OAuth授权记录信息
        $result->oauthInfo;
    }
}
```