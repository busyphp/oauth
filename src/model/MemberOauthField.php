<?php

namespace BusyPHP\oauth\model;

use BusyPHP\model\Field;

/**
 * 三方登录字段
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/7/9 下午2:53 下午 MemberOauthField.php $
 */
class MemberOauthField extends Field
{
    /**
     * @var int
     */
    public $id = null;
    
    /**
     * 会员ID
     * @var int
     */
    public $userId = null;
    
    /**
     * 登录类型
     * @var int
     */
    public $type = null;
    
    /**
     * 厂商类型
     * @var int
     */
    public $unionType = null;
    
    /**
     * openid
     * @var string
     */
    public $openid = null;
    
    /**
     * 同登录类型唯一值
     * @var string
     */
    public $unionid = null;
    
    /**
     * 绑定时间
     * @var int
     */
    public $createTime = null;
    
    /**
     * 更新时间
     * @var int
     */
    public $updateTime = null;
    
    /**
     * 登录次数
     * @var int
     */
    public $loginTotal = null;
    
    /**
     * 登录IP
     * @var string
     */
    public $loginIp = null;
    
    /**
     * 上次登录时间
     * @var int
     */
    public $lastTime = null;
    
    /**
     * 登录时间
     * @var int
     */
    public $loginTime = null;
    
    /**
     * 上次登录IP
     * @var string
     */
    public $lastIp = null;
    
    /**
     * 昵称
     * @var string
     */
    public $nickname = null;
    
    /**
     * 性别
     * @var int
     */
    public $sex = null;
    
    /**
     * 头像
     * @var string
     */
    public $avatar = null;
    
    /**
     * 登录数据
     * @var array
     */
    public $userInfo = null;
}