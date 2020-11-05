<?php

namespace BusyPHP\oauth\interfaces;

use BusyPHP\exception\AppException;
use BusyPHP\Model;
use BusyPHP\model\Field;

/**
 * OAuth登录模型接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/7 下午2:58 下午 OAuthModel.php $
 * @mixin Model
 */
interface OAuthModel
{
    /**
     * 执行OAuth注册账户，注意内部无需启用事物，但是业务流程中需要加锁的依然要加锁
     * @param Field $field 注册的数据
     * @return int 注册后的用户ID
     * @throws AppException
     */
    public function onOAuthRegister(Field $field);
    
    
    /**
     * 执行OAuth更新账户，注意内部无需启用事物，但是业务流程中需要加锁的依然要加锁
     * @param int   $userId 会员ID
     * @param Field $field 更新的数据
     */
    public function onOAuthUpdate($userId, Field $field);
    
    
    /**
     * 执行OAuth登录，注意内部无需启用事物，但是业务流程中需要加锁的依然要加锁
     * @param int       $userId 会员ID
     * @param OAuthInfo $api 三方登录数据
     * @param array     $info 绑定的记录数据
     * @return array|false 返回false代表必须执行手动数据填充，如：未注册手机号；返回数组则认为是会员数据
     * @throws AppException
     */
    public function onOAuthLogin($userId, OAuthInfo $api, array $info);
}