<?php

namespace BusyPHP\oauth\interfaces;

use BusyPHP\model\Field;
use BusyPHP\oauth\model\info\MemberOauthInfo;

/**
 * 执行OAuth绑定或注册回调
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/4/5 下午1:18 下午 OnOAuthBindOrRegisterCallback.php $
 */
interface OnOAuthBindOrRegisterCallback
{
    /**
     * 执行注册查重
     * 1. 返回用户ID代表用户已注册，执行OAuth绑定
     * 2. 返回0代表用户未注册，则执行注册，会触发{@see OnOAuthBindOrRegisterCallback::onGetRegisterField()}
     * @param OAuth $oauth
     * @return int
     */
    public function onCheckRegisterRepeat(OAuth $oauth) : int;
    
    
    /**
     * 返回注册数据
     * @param OAuth $oauth
     * @return Field
     */
    public function onGetRegisterField(OAuth $oauth) : Field;
    
    
    /**
     * 返回更新数据
     * @param MemberOauthInfo $oauthInfo
     * @return Field
     */
    public function onGetUpdateField(MemberOauthInfo $oauthInfo) : Field;
}