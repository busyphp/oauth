<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\interfaces;

use BusyPHP\helper\ArrayHelper;
use BusyPHP\helper\ClassHelper;
use BusyPHP\model\ObjectOption;
use BusyPHP\oauth\Driver;
use RangeException;

/**
 * 三方授权数据实体
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:12 OAuthInfo.php $
 */
class OAuthInfo extends ObjectOption
{
    /** @var int 未知 */
    const SEX_UNKNOWN = 0;
    
    /** @var int 男 */
    const SEX_MAN = 1;
    
    /** @var int 女 */
    const SEX_WOMAN = 2;
    
    /**
     * openid
     * @var string
     */
    protected string $openId = '';
    
    /**
     * 厂商联合ID
     * @var string
     */
    protected string $unionId = '';
    
    /**
     * 厂商类型
     * @var string
     */
    protected string $union = '';
    
    /**
     * 昵称
     * @var string
     */
    protected string $nickname = '';
    
    /**
     * 头像
     * @var string
     */
    protected string $avatar = '';
    
    /**
     * 性别
     * @var int
     */
    protected int $sex = self::SEX_UNKNOWN;
    
    /**
     * 登录类型
     * @var string
     */
    protected string $type = '';
    
    /**
     * 用户信息
     * @var array
     */
    protected array $userInfo = [];
    
    /**
     * 三方APPID
     * @var string
     */
    protected string $appId = '';
    
    
    /**
     * 构造函数
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->type  = $driver->getType();
        $this->union = $driver->getUnion();
        $this->appId = $driver->getAppId();
        
        parent::__construct();
    }
    
    
    /**
     * 设置会员数据
     * @param array $userInfo
     * @return $this
     */
    public function setUserInfo(array $userInfo) : static
    {
        $this->userInfo = $userInfo;
        
        return $this;
    }
    
    
    /**
     * 设置openid
     * @param mixed $openId
     * @return $this
     */
    public function setOpenId(mixed $openId) : static
    {
        $this->openId = (string) $openId;
        
        return $this;
    }
    
    
    /**
     * 设置同类型登录方式唯一值
     * @param mixed $unionId
     * @return $this
     */
    public function setUnionId(mixed $unionId) : static
    {
        $this->unionId = (string) $unionId;
        
        return $this;
    }
    
    
    /**
     * 设置头像
     * @param mixed $avatar
     * @return $this
     */
    public function setAvatar(mixed $avatar) : static
    {
        $this->avatar = (string) $avatar;
        
        return $this;
    }
    
    
    /**
     * 设置昵称
     * @param mixed $nickname
     * @return $this
     */
    public function setNickname(mixed $nickname) : static
    {
        $this->nickname = (string) $nickname;
        
        return $this;
    }
    
    
    /**
     * 设置性别
     * @param int $sex
     * @return $this
     */
    public function setSex(int $sex) : static
    {
        if (!in_array($sex, array_keys(static::getSexMap()))) {
            throw new RangeException('Gender is not allowed');
        }
        
        $this->sex = $sex;
        
        return $this;
    }
    
    
    /**
     * 获取 openid
     * @return string
     */
    public function getOpenId() : string
    {
        return $this->openId;
    }
    
    
    /**
     * 获取 unionId
     * @return string
     */
    public function getUnionId() : string
    {
        return $this->unionId;
    }
    
    
    /**
     * 获取厂商类型
     * @return string
     */
    public function getUnion() : string
    {
        return $this->union;
    }
    
    
    /**
     * 获取用户昵称
     * @return string
     */
    public function getNickname() : string
    {
        return $this->nickname;
    }
    
    
    /**
     * 获取头像
     * @return string
     */
    public function getAvatar() : string
    {
        return $this->avatar;
    }
    
    
    /**
     * 获取性别，1男 2女
     * @return int
     */
    public function getSex() : int
    {
        return $this->sex;
    }
    
    
    /**
     * 获取登录类型
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    
    
    /**
     * 获取三方数据
     * @return array
     */
    public function getUserInfo() : array
    {
        return $this->userInfo;
    }
    
    
    /**
     * 获取三方APPID
     * @return string
     */
    public function getAppId() : string
    {
        return $this->appId;
    }
    
    
    /**
     * 解析性别
     * @param mixed $sex
     * @return int
     */
    public static function parseSex(mixed $sex) : int
    {
        $sex = trim((string) $sex);
        if (is_numeric($sex)) {
            return match (intval($sex)) {
                1       => self::SEX_MAN,
                2       => self::SEX_WOMAN,
                default => self::SEX_UNKNOWN,
            };
        } else {
            if (str_contains($sex, '男')) {
                return self::SEX_MAN;
            } elseif (str_contains($sex, '女')) {
                return self::SEX_WOMAN;
            }
            
            return self::SEX_UNKNOWN;
        }
    }
    
    
    /**
     * 获取性别集合
     * @param int|null $sex
     * @return array|string
     */
    public static function getSexMap(int $sex = null) : array|string
    {
        return ArrayHelper::getValueOrSelf(ClassHelper::getConstAttrs(self::class, 'SEX_', ClassHelper::ATTR_NAME), $sex);
    }
}