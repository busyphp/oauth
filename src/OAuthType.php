<?php

namespace BusyPHP\oauth;

/**
 * 三方登录类型
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/8 下午10:59 上午 OAuthType.php $
 */
class OAuthType
{
    // +----------------------------------------------------
    // + 登录类型
    // +----------------------------------------------------
    /**
     * 微信公众号登录
     */
    const TYPE_WECHAT_PUBLIC = 1;
    
    /**
     * 微信APP登录
     */
    const TYPE_WECHAT_APP = 2;
    
    /**
     * 微信小程序登录
     */
    const TYPE_WECHAT_MIME = 3;
    
    /**
     * QQ网页端登录
     */
    const TYPE_QQ_WEB = 10;
    
    /**
     * QQAPP端登录
     */
    const TYPE_QQ_APP = 11;
    
    /**
     * QQ小程序登录
     */
    const TYPE_QQ_MIME = 12;
    
    /**
     * 支付宝网页端
     */
    const TYPE_ALIPAY_WEB = 20;
    
    /**
     * 支付宝公众号登录
     */
    const TYPE_ALIPAY_PUBLIC = 21;
    
    /**
     * 支付宝小程序登录
     */
    const TYPE_ALIPAY_MIME = 22;
    
    /**
     * 支付宝APP端登录
     */
    const TYPE_ALIPAY_APP = 23;
    
    /**
     * 新浪微博网页端登录
     */
    const TYPE_SINA_WEB = 30;
    
    /**
     * 新浪微博APP端登录
     */
    const TYPE_SINA_APP = 31;
    
    /**
     * GITHUB网页端登录
     */
    const TYPE_GITHUB_WEB = 40;
    
    /**
     * GITHUBAPP端登录
     */
    const TYPE_GITHUB_APP = 41;
    
    /**
     * 苹果网页端登录
     */
    const TYPE_APPLE_WEB = 50;
    
    /**
     * 苹果APP端登录
     */
    const TYPE_APPLE_APP = 51;
    
    /**
     * 百度网页端登录
     */
    const TYPE_BAIDU_WEB = 60;
    
    /**
     * 百度APP端登录
     */
    const TYPE_BAIDU_APP = 61;
    
    /**
     * 百度小程序登录
     */
    const TYPE_BAIDU_MIME = 62;
    
    /**
     * 淘宝网页端登录
     */
    const TYPE_TAOBAO_WEB = 70;
    
    /**
     * 淘宝APP端登录
     */
    const TYPE_TAOBAO_APP = 71;
    
    // +----------------------------------------------------
    // + 厂商
    // +----------------------------------------------------
    /**
     * 微信
     */
    const COMPANY_WECHAT = 1;
    
    /**
     * QQ
     */
    const COMPANY_QQ = 2;
    
    /**
     * 支付宝
     */
    const COMPANY_ALIPAY = 3;
    
    /**
     * 新浪微博
     */
    const COMPANY_SINA = 4;
    
    /**
     * GITHUB
     */
    const COMPANY_GITHUB = 5;
    
    /**
     * 苹果
     */
    const COMPANY_APPLE = 6;
    
    /**
     * 百度
     */
    const COMPANY_BAIDU = 7;
    
    /**
     * 淘宝
     */
    const COMPANY_TAOBAO = 8;
}