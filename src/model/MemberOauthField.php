<?php

namespace BusyPHP\oauth\model;

use BusyPHP\model\Field;

/**
 * 三方登录字段
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/7/9 下午2:53 下午 MemberOauthField.php $
 * @method static mixed id($op = null, $value = null) ID
 * @method static mixed userId($op = null, $value = null) 会员ID
 * @method static mixed type($op = null, $value = null) 登录类型
 * @method static mixed unionType($op = null, $value = null) 厂商类型
 * @method static mixed openid($op = null, $value = null) openid
 * @method static mixed unionid($op = null, $value = null) 同登录类型唯一值
 * @method static mixed createTime($op = null, $value = null) 绑定时间
 * @method static mixed updateTime($op = null, $value = null) 更新时间
 * @method static mixed loginTotal($op = null, $value = null) 登录次数
 * @method static mixed loginIp($op = null, $value = null) 本次登录IP
 * @method static mixed lastIp($op = null, $value = null) 上次登录IP
 * @method static mixed loginTime($op = null, $value = null) 本次登录时间
 * @method static mixed lastTime($op = null, $value = null) 上次登录时间
 * @method static mixed nickname($op = null, $value = null) 昵称
 * @method static mixed avatar($op = null, $value = null) 头像
 * @method static mixed sex($op = null, $value = null) 性别
 * @method static mixed userInfo($op = null, $value = null) 登录数据
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