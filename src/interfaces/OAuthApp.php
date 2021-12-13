<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\interfaces;

/**
 * APP授权登录基本接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:11 OAuthApp.php $
 */
abstract class OAuthApp implements OAuth
{
    /**
     * 数据
     * @var mixed
     */
    protected $data;
    
    /**
     * 三方账户ID
     * @var string
     */
    private $accountId;
    
    
    /**
     * OAuthAPP constructor.
     * @param mixed  $data 三方登录数据
     * @param string $accountId 三方账户ID，用于区分同一种登录方式，不同账户
     */
    public function __construct($data = null, string $accountId = '')
    {
        $this->data      = $data;
        $this->accountId = $accountId;
    }
    
    
    /**
     * 执行申请授权
     * @param string $redirectUri 回调地址
     */
    public function onApplyAuth(string $redirectUri)
    {
    }
    
    
    /**
     * 换取票据
     * @return string
     */
    public function onGetAccessToken() : string
    {
        return '';
    }
}