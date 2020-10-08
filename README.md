BusyPHP OAuth
===============

## 说明

三方登录插件

## 安装
```
composer require busyphp/oauth
```

## 数据库安装
> 安装成功后通过浏览器访问以下连接进行数据库安装
```
http://域名/general/plugin/install/oauth
```

## 配置文件
> 配置文件存放到 `/config/extend/oauth.php`
```php
return [
    // 会员模型类绑定，该模型必须集成 BusyPHP\oauth\interfaces\OAuthModel 接口
    'member' => Member::class,
    
    // 登录接口绑定
    'types'  => [
        // 登录类型(int) => [
        //     'name' => '登录类型名称',
        //     'class' => '登录接口类，该类必须集成 BusyPHP\oauth\interfaces\OAuth 接口'
        // ]
    ]
];
```

## 接口说明

### OAuth2.0接口类

三方登录接口需要集成该接口

> `BusyPHP\oauth\interfaces\OAuth`

### APP授权登录基本接口

三方登录接口需要集成该接口

> `BusyPHP\oauth\interfaces\OAuthApp`

### 模型接口

会员模型需要集成该接口

> `BusyPHP\oauth\interfaces\OAuthModel`

## 使用方法

> 待完善