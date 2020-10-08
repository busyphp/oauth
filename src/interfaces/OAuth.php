<?php

namespace BusyPHP\oauth\interfaces {
    
    use BusyPHP\exception\ParamInvalidException;
    
    /**
     * OAuth2.0接口类，所有的OAuth接口都需要集成该接口
     * @author busy^life <busy.life@qq.com>
     * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
     * @version $Id: 2020/7/8 下午11:14 上午 OAuth.php $
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
         * @return OAuth_Info
         */
        public function onGetInfo();
        
        
        /**
         * 验证是否可以更新头像
         * @param string $avatar 用户已设置的头像地址
         * @return bool
         */
        public function canUpdateAvatar($avatar) : bool;
    }
    
    
    /**
     * OAuth用户信息容器
     * @package core\interfaces\oauth
     */
    class OAuth_Info
    {
        /**
         * 未知
         */
        const SEX_UNKNOWN = 0;
        
        /**
         * 男
         */
        const SEX_MAN = 1;
        
        /**
         * 女
         */
        const SEX_WOMAN = 2;
        
        private $openId    = '';
        
        private $unionId   = '';
        
        private $unionType = 0;
        
        private $nickname  = '';
        
        private $avatar    = '';
        
        private $sex       = self::SEX_UNKNOWN;
        
        private $type      = 0;
        
        private $userInfo  = [];
        
        
        /**
         * OAuth_Info constructor.
         * @param OAuth|null $oauth
         */
        public function __construct(OAuth $oauth = null)
        {
            if ($oauth != null) {
                $this->setType($oauth->getType());
                $this->setUnionType($oauth->getUnionType());
            }
        }
        
        
        /**
         * 设置会员数据
         * @param array $userInfo
         * @return $this
         */
        public function setUserInfo($userInfo) : self
        {
            $this->userInfo = $userInfo;
            
            return $this;
        }
        
        
        /**
         * 设置openid
         * @param string $openId
         * @return $this
         */
        public function setOpenId($openId) : self
        {
            $this->openId = trim($openId);
            
            return $this;
        }
        
        
        /**
         * 设置同类型登录方式唯一值
         * @param string $unionId
         * @return $this
         */
        public function setUnionId($unionId) : self
        {
            $this->unionId = trim($unionId);
            
            return $this;
        }
        
        
        /**
         * 设置头像
         * @param string $avatar
         * @return $this
         */
        public function setAvatar($avatar) : self
        {
            $this->avatar = trim($avatar);
            
            return $this;
        }
        
        
        /**
         * 设置昵称
         * @param string $nickname
         * @return $this
         */
        public function setNickname($nickname) : self
        {
            $this->nickname = trim($nickname);
            
            return $this;
        }
        
        
        /**
         * 设置性别
         * @param int $sex
         * @return $this
         * @throws ParamInvalidException
         */
        public function setSex($sex = self::SEX_UNKNOWN) : self
        {
            $this->sex = intval($sex);
            if (!in_array($this->sex, [self::SEX_MAN, self::SEX_WOMAN, self::SEX_UNKNOWN])) {
                throw new ParamInvalidException('sex');
            }
            
            return $this;
        }
        
        
        /**
         * 设置登录类型
         * @param int $type
         * @return $this
         */
        public function setType($type) : self
        {
            $this->type = intval($type);
            
            return $this;
        }
        
        
        /**
         * 设置登录厂商
         * @param int $unionType
         * @return $this
         */
        public function setUnionType($unionType) : self
        {
            $this->unionType = intval($unionType);
            
            return $this;
        }
        
        
        /**
         * 解析性别
         * @param string|int $sex
         * @return int
         */
        public static function parseSex($sex) : int
        {
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
                $sex = trim($sex);
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
        public function getOpenId()
        {
            return $this->openId;
        }
        
        
        /**
         * 获取 unionid
         * @return string
         */
        public function getUnionId()
        {
            return $this->unionId;
        }
        
        
        /**
         * 获取厂商类型
         * @return int
         */
        public function getUnionType()
        {
            return $this->unionType;
        }
        
        
        /**
         * 获取用户昵称
         * @return string
         */
        public function getNickname()
        {
            return $this->nickname;
        }
        
        
        /**
         * 获取头像
         * @return string
         */
        public function getAvatar()
        {
            return $this->avatar;
        }
        
        
        /**
         * 获取性别，1男 2女
         * @return int
         */
        public function getSex()
        {
            return $this->sex;
        }
        
        
        /**
         * 获取登录类型
         * @return int
         */
        public function getType()
        {
            return $this->type;
        }
        
        
        /**
         * 获取三方数据
         * @return array
         */
        public function getUserInfo()
        {
            return $this->userInfo;
        }
    }
}

