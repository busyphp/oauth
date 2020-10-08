<?php

namespace BusyPHP\oauth\interfaces {
    
    /**
     * APP授权登录基本接口
     * @author busy^life <busy.life@qq.com>
     * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
     * @version $Id: 2020/7/9 下午7:45 下午 OAuthAPP.php $
     */
    abstract class OAuthApp implements OAuth
    {
        /**
         * 数据
         * @var OAuthApp_Data
         */
        protected $data;
        
        
        /**
         * OAuthAPP constructor.
         * @param OAuthApp_Data $data 三方给的数据
         */
        public function __construct(OAuthApp_Data $data)
        {
            $this->data = $data;
        }
        
        
        /**
         * 执行申请授权
         * @param string $redirectUri 回调地址
         */
        public function onApplyAuth($redirectUri)
        {
        }
        
        
        /**
         * 换取票据
         * @return string
         */
        public function onGetAccessToken()
        {
            return '';
        }
    }
    
    
    /**
     * Class OAuthApp_Data
     * @package core\interfaces\oauth
     */
    abstract class OAuthApp_Data
    {
        /**
         * 获取数据
         * @return array
         */
        abstract function getData();
    }
}