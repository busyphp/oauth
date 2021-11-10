<?php

namespace BusyPHP\oauth\model\info;

use BusyPHP\model\Entity;
use BusyPHP\oauth\model\MemberOauthField;

/**
 * OAuth模型信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:51 MemberOauthInfo.php $
 * @method static Entity decodeUserInfo($op = null, $value = null) 登录数据
 */
class MemberOauthInfo extends MemberOauthField
{
    /**
     * 解码的三方登录数据
     * @var array
     */
    public $decodeUserInfo;
    
    
    public function onParseAfter()
    {
        $this->decodeUserInfo = json_decode($this->userInfo, true);
        $this->type           = (int) $this->type;
        $this->unionType      = (int) $this->unionType;
    }
}