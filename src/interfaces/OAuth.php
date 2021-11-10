<?php

namespace BusyPHP\oauth\interfaces;

/**
 * OAuth2.0接口类，所有的OAuth接口都需要集成该接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:11 OAuth.php $
 */
interface OAuth
{
    /**
     * 获取登录类型
     * @return int
     */
    public function getType();
    
    
    /**
     * 获取厂商类型
     * @return int
     */
    public function getUnionType();
    
    
    /**
     * 执行申请授权
     * @param string $redirectUri 回调地址
     */
    public function onApplyAuth($redirectUri);
    
    
    /**
     * 换取票据
     * @return string
     */
    public function onGetAccessToken();
    
    
    /**
     * 获取用户信息，该方法可能会多次触发，请自行处理重复处理锁
     * @return OAuthInfo
     */
    public function onGetInfo();
    
    
    /**
     * 验证是否可以更新头像
     * @param string $avatar 用户已设置的头像地址
     * @return bool
     */
    public function canUpdateAvatar($avatar) : bool;
}

