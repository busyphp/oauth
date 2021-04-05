<?php

namespace BusyPHP\oauth\interfaces;

use BusyPHP\model\Field;

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
     * @return int
     */
    public function onCheckRegisterRepeat() : int;
    
    
    /**
     * 返回注册数据
     * @return Field
     */
    public function onGetRegisterField() : Field;
    
    
    /**
     * 返回更新数据
     * @return Field
     */
    public function onGetUpdateField() : Field;
}