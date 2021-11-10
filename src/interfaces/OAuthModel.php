<?php

namespace BusyPHP\oauth\interfaces;

use BusyPHP\Model;
use BusyPHP\model\Field;
use BusyPHP\oauth\model\info\MemberOauthInfo;
use Throwable;

/**
 * OAuth登录模型接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:12 OAuthModel.php $
 * @mixin Model
 */
interface OAuthModel
{
    /**
     * 执行OAuth注册账户，注意内部无需启用事物
     * @param Field $field 注册的数据
     * @return int 注册后的用户ID
     * @throws Throwable
     */
    public function onOAuthRegister(Field $field) : int;
    
    
    /**
     * 执行OAuth登录，注意内部无需启用事物
     * 需要用户完善数据，如绑定手机号，则可以通过抛出自定义异常解决
     * @param MemberOauthInfo $oauthInfo 绑定的记录数据
     * @param OAuth           $oauthApi 三方登录接口
     * @return Field 返回信息由用户模型自定义
     * @throws Throwable
     */
    public function onOAuthLogin(MemberOauthInfo $oauthInfo, OAuth $oauthApi) : Field;
}