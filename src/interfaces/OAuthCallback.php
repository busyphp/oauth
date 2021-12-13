<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\interfaces;

use BusyPHP\model\Field;

/**
 * 执行OAuth绑定或注册回调
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:11 OAuthCallback.php $
 */
interface OAuthCallback
{
    /**
     * 执行注册校验
     * @param OAuth $oauth
     * @return int 返回用户ID代表已注册，则执行绑定，返回0代表用户未注册，则执行注册
     */
    public function onCheckRegister(OAuth $oauth) : int;
    
    
    /**
     * 获取注册的用户数据
     * @param OAuth $oauth
     * @return Field
     */
    public function onGetRegisterData(OAuth $oauth) : Field;
    
    
    /**
     * 获取更新的用户数据
     * @param OAuth $oauth
     * @return Field|null
     */
    public function onGetUpdateData(OAuth $oauth) : ?Field;
}