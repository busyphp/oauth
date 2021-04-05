<?php

namespace BusyPHP\oauth\model\info;

use BusyPHP\oauth\model\MemberOauthField;

/**
 * OAuth模型信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/4/5 下午12:58 下午 MemberOauthInfo.php $
 *
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
    
    
    /**
     * 获取登录方式类型
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }
    
    
    /**
     * 获取登录厂商
     * @return int
     */
    public function getUnionType() : int
    {
        return $this->unionType;
    }
}