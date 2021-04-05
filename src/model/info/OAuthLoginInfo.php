<?php

namespace BusyPHP\oauth\model\info;

use BusyPHP\model\Field;

/**
 * OAuth登录返回信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/4/5 下午12:41 下午 OAuthLoginInfo.php $
 */
class OAuthLoginInfo
{
    /**
     * @var Field
     */
    public $modelInfo;
    
    /**
     * @var MemberOauthInfo
     */
    public $oauthInfo;
}