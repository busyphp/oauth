三方登录模块
===============

## 说明

用于BusyPHP进行三方登录的基本组件，支持目前主流的三方登录

## 安装
```
composer require busyphp/oauth
```

> 安装成功后在后台 > 开发模式 > 插件管理 进行数据表安装/卸载

## 配置 `/config/extend/oauth.php`
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

```php
<?php
use BusyPHP\Controller;
use BusyPHP\model\Field;
use BusyPHP\oauth\interfaces\OAuth;
use BusyPHP\oauth\interfaces\OnOAuthBindOrRegisterCallback;
use BusyPHP\oauth\model\MemberOauth;

class Index extends Controller
{
    public function index()
    {
        $loginInfo = MemberOauth::init()->login(null, new class implements OnOAuthBindOrRegisterCallback {
            /**
             * 执行注册校验
             * @param OAuth $oauth
             * @return int 返回用户ID代表已注册，则执行绑定，返回0代表用户未注册，则执行注册
             */
            public function onCheckRegister(OAuth $oauth) : int
            {
                // 已注册，返回用户ID，
                // 未注册返回 0
                return 0;
            }
    
    
            /**
             * 返回要注册的用户数据
             * @param OAuth $oauth
             * @return Field
             */
            public function onGetRegisterField(OAuth $oauth) : Field
            {
                // 获取到三方登录数据
                $authInfo = $oauth->onGetInfo();
                
                // 构建注册信息
                $field = new MemberField();
                $field->setNickname($authInfo->getNickname());
                $field->setSex($authInfo->getSex());
                $field->setAvatar($authInfo->getAvatar());
                
                return $field;
            }
        });
        
        // 会员模型返回的登录信息
        $loginInfo->modelInfo;
        
        // OAuth绑定的登录信息
        $loginInfo->oauthInfo;
    }
}
```