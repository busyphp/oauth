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
     * 返回要执行注册的数据
     * @param OAuth $oauth
     * @return Field
     */
    public function onGetRegisterField(OAuth $oauth) : Field;
    
    
    /**
     * 返回要执行更新的数据
     * 比如用户头像依赖三方头像，那么执行绑定的时候会拿到最新的头像，可以在此时更新
     * # 感觉没啥意义，因为在执行 {@see OAuthModel::onOAuthLogin()} 的时候也可以进行更新
     * @param MemberOauthInfo $oauthInfo
     * @return Field
     */
    public function onGetUpdateField(MemberOauthInfo $oauthInfo) : Field;
}