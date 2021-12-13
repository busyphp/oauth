<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\interfaces;

use BusyPHP\Model;
use BusyPHP\model\ObjectOption;
use RangeException;

/**
 * OAuth用户信息容器
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
    private $openId = '';
    
    /**
     * 厂商联合ID
     * @var string
     */
    private $unionId = '';
    
    /**
     * 厂商类型
     * @var int
     */
    private $unionType = 0;
    
    /**
     * 昵称
     * @var string
     */
    private $nickname = '';
    
    /**
     * 头像
     * @var string
     */
    private $avatar = '';
    
    /**
     * 性别
     * @var int
     */
    private $sex = self::SEX_UNKNOWN;
    
    /**
     * 登录类型
     * @var int
     */
    private $type = 0;
    
    /**
     * 用户信息
     * @var array
     */
    private $userInfo = [];
    
    /**
     * 三方APPID
     * @var string
     */
    private $appId = '';
    
    
    /**
     * OAuth_Info constructor.
     * @param OAuth|null $oauth
     */
    public function __construct(OAuth $oauth = null)
    {
        if ($oauth != null) {
            $this->setType($oauth->getType());
            $this->setUnionType($oauth->getUnionType());
            $this->setAppId($oauth->getAppId());
        }
    }
    
    
    /**
     * 设置会员数据
     * @param array $userInfo
     * @return $this
     */
    public function setUserInfo(array $userInfo) : self
    {
        $this->userInfo = $userInfo;
        
        return $this;
    }
    
    
    /**
     * 设置openid
     * @param string $openId
     * @return $this
     */
    public function setOpenId(string $openId) : self
    {
        $this->openId = trim($openId);
        
        return $this;
    }
    
    
    /**
     * 设置同类型登录方式唯一值
     * @param string $unionId
     * @return $this
     */
    public function setUnionId(string $unionId) : self
    {
        $this->unionId = trim($unionId);
        
        return $this;
    }
    
    
    /**
     * 设置头像
     * @param string $avatar
     * @return $this
     */
    public function setAvatar(string $avatar) : self
    {
        $this->avatar = trim($avatar);
        
        return $this;
    }
    
    
    /**
     * 设置昵称
     * @param string $nickname
     * @return $this
     */
    public function setNickname(string $nickname) : self
    {
        $this->nickname = trim($nickname);
        
        return $this;
    }
    
    
    /**
     * 设置性别
     * @param int $sex
     * @return $this
     */
    public function setSex(int $sex = self::SEX_UNKNOWN) : self
    {
        $this->sex = intval($sex);
        if (!in_array($this->sex, [self::SEX_MAN, self::SEX_WOMAN, self::SEX_UNKNOWN])) {
            throw new RangeException('Gender is not allowed');
        }
        
        return $this;
    }
    
    
    /**
     * 设置登录类型
     * @param int $type
     * @return $this
     */
    public function setType(int $type) : self
    {
        $this->type = $type;
        
        return $this;
    }
    
    
    /**
     * 设置登录厂商
     * @param int $unionType
     * @return $this
     */
    public function setUnionType(int $unionType) : self
    {
        $this->unionType = $unionType;
        
        return $this;
    }
    
    
    /**
     * 解析性别
     * @param mixed $sex
     * @return int
     */
    public static function parseSex($sex) : int
    {
        $sex = trim((string) $sex);
        if (is_numeric($sex)) {
            switch (intval($sex)) {
                case 1:
                    return self::SEX_MAN;
                break;
                case 2:
                    return self::SEX_WOMAN;
                break;
                default:
                    return self::SEX_UNKNOWN;
            }
        } else {
            if (false !== strpos($sex, '男')) {
                return self::SEX_MAN;
            } elseif (false !== strpos($sex, '女')) {
                return self::SEX_WOMAN;
            }
            
            return self::SEX_UNKNOWN;
        }
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
     * 获取 unionid
     * @return string
     */
    public function getUnionId() : string
    {
        return $this->unionId;
    }
    
    
    /**
     * 获取厂商类型
     * @return int
     */
    public function getUnionType() : int
    {
        return $this->unionType;
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
     * @return int
     */
    public function getType() : int
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
     * 设置三方APPID
     * @param string $appId
     * @return $this
     */
    public function setAppId(string $appId) : self
    {
        $this->appId = $appId;
        
        return $this;
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
     * 获取性别集合
     * @param int|null $val
     * @return array|string
     */
    public static function getSexs(?int $val = null)
    {
        return Model::parseVars(Model::parseConst(self::class, 'SEX_', [], function($item) {
            return $item['name'];
        }), $val);
    }
}