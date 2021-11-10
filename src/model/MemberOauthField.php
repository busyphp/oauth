<?php

namespace BusyPHP\oauth\model;

use BusyPHP\model\Entity;
use BusyPHP\model\Field;

/**
 * 三方登录字段
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:51 MemberOauthField.php $
 * @method static Entity id($op = null, $value = null) ID
 * @method static Entity userId($op = null, $value = null) 会员ID
 * @method static Entity type($op = null, $value = null) 登录类型
 * @method static Entity unionType($op = null, $value = null) 厂商类型
 * @method static Entity openid($op = null, $value = null) openid
 * @method static Entity unionid($op = null, $value = null) 同登录类型唯一值
 * @method static Entity createTime($op = null, $value = null) 绑定时间
 * @method static Entity updateTime($op = null, $value = null) 更新时间
 * @method static Entity loginTotal($op = null, $value = null) 登录次数
 * @method static Entity loginIp($op = null, $value = null) 本次登录IP
 * @method static Entity lastIp($op = null, $value = null) 上次登录IP
 * @method static Entity loginTime($op = null, $value = null) 本次登录时间
 * @method static Entity lastTime($op = null, $value = null) 上次登录时间
 * @method static Entity nickname($op = null, $value = null) 昵称
 * @method static Entity avatar($op = null, $value = null) 头像
 * @method static Entity sex($op = null, $value = null) 性别
 * @method static Entity userInfo($op = null, $value = null) 登录数据
 */
class MemberOauthField extends Field
{
    /**
     * ID
     * @var int
     */
    public $id;
    
    /**
     * 会员ID
     * @var int
     */
    public $userId;
    
    /**
     * 登录类型
     * @var int
     */
    public $type;
    
    /**
     * 厂商类型
     * @var int
     */
    public $unionType;
    
    /**
     * openid
     * @var string
     */
    public $openid;
    
    /**
     * 同登录类型唯一值
     * @var string
     */
    public $unionid;
    
    /**
     * 绑定时间
     * @var int
     */
    public $createTime;
    
    /**
     * 更新时间
     * @var int
     */
    public $updateTime;
    
    /**
     * 登录次数
     * @var int
     */
    public $loginTotal;
    
    /**
     * 本次登录IP
     * @var string
     */
    public $loginIp;
    
    /**
     * 上次登录IP
     * @var string
     */
    public $lastIp;
    
    /**
     * 本次登录时间
     * @var int
     */
    public $loginTime;
    
    /**
     * 上次登录时间
     * @var int
     */
    public $lastTime;
    
    /**
     * 昵称
     * @var string
     */
    public $nickname;
    
    /**
     * 头像
     * @var string
     */
    public $avatar;
    
    /**
     * 性别
     * @var int
     */
    public $sex;
    
    /**
     * 登录数据
     * @var string
     */
    public $userInfo;
}