<?php
/**
 * OAuth登录配置
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/7 下午2:48 下午 oauth.php $
 */

return [
    // 会员模型类绑定，该模型必须集成 BusyPHP\oauth\interfaces\OAuthModel 接口
    'member' => '',
    
    // 登录接口绑定
    'types'  => [
        // 登录类型(int) => [
        //     'name' => '登录类型名称',
        //     'class' => '登录接口类，该类必须集成 BusyPHP\oauth\interfaces\OAuth 接口'
        // ]
    ]
];