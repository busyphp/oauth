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
     * @var OAuthAppData
     */
    protected $data;
    
    
    /**
     * OAuthAPP constructor.
     * @param OAuthAppData $data 三方给的数据
     */
    public function __construct(OAuthAppData $data)
    {
        $this->data = $data;
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