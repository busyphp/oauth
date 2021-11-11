<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\model\info;

use BusyPHP\model\Field;

/**
 * OAuth登录返回信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:52 OAuthLoginInfo.php $
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