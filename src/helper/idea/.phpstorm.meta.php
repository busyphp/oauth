<?php
/**
 * PHPStorm辅助
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/4/5 下午2:15 下午 .phpstorm.meta.php $
 */

namespace PHPSTORM_META {
    
    registerArgumentsSet('oauth_type', \BusyPHP\oauth\OAuthType::TYPE_WECHAT_PUBLIC,
        \BusyPHP\oauth\OAuthType::TYPE_WECHAT_APP,
        \BusyPHP\oauth\OAuthType::TYPE_WECHAT_MIME,
        \BusyPHP\oauth\OAuthType::TYPE_ALIPAY_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_ALIPAY_MIME,
        \BusyPHP\oauth\OAuthType::TYPE_ALIPAY_PUBLIC,
        \BusyPHP\oauth\OAuthType::TYPE_ALIPAY_APP,
        \BusyPHP\oauth\OAuthType::TYPE_APPLE_APP,
        \BusyPHP\oauth\OAuthType::TYPE_APPLE_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_BAIDU_APP,
        \BusyPHP\oauth\OAuthType::TYPE_BAIDU_MIME,
        \BusyPHP\oauth\OAuthType::TYPE_BAIDU_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_GITHUB_APP,
        \BusyPHP\oauth\OAuthType::TYPE_GITHUB_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_QQ_APP,
        \BusyPHP\oauth\OAuthType::TYPE_QQ_MIME,
        \BusyPHP\oauth\OAuthType::TYPE_QQ_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_SINA_APP,
        \BusyPHP\oauth\OAuthType::TYPE_SINA_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_TAOBAO_WEB,
        \BusyPHP\oauth\OAuthType::TYPE_TAOBAO_APP
    );
    
    registerArgumentsSet('oauth_company', \BusyPHP\oauth\OAuthType::TYPE_WECHAT_PUBLIC,
        \BusyPHP\oauth\OAuthType::COMPANY_WECHAT,
        \BusyPHP\oauth\OAuthType::COMPANY_ALIPAY,
        \BusyPHP\oauth\OAuthType::COMPANY_APPLE,
        \BusyPHP\oauth\OAuthType::COMPANY_BAIDU,
        \BusyPHP\oauth\OAuthType::COMPANY_GITHUB,
        \BusyPHP\oauth\OAuthType::COMPANY_QQ,
        \BusyPHP\oauth\OAuthType::COMPANY_SINA,
        \BusyPHP\oauth\OAuthType::COMPANY_TAOBAO);
    
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::getInfoByUserId(), 1, argumentsSet('oauth_type'));
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::checkBindByUserId(), 1, argumentsSet('oauth_type'));
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::getInfoByOpenId(), 1, argumentsSet('oauth_type'));
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::checkBindByOpenid(), 1, argumentsSet('oauth_type'));
    
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::getInfoByUserId(), 2, argumentsSet('oauth_company'));
    
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::getInfoByUnionId(), 1, argumentsSet('oauth_company'));
    expectedArguments(\BusyPHP\oauth\model\MemberOauth::checkBindByUnionId(), 1, argumentsSet('oauth_company'));
    
    
    expectedReturnValues(\BusyPHP\oauth\interfaces\OAuthInfo::getType(), argumentsSet('oauth_type'));
    expectedReturnValues(\BusyPHP\oauth\model\info\MemberOauthInfo::getType(), argumentsSet('oauth_type'));
    expectedReturnValues(\BusyPHP\oauth\interfaces\OAuthInfo::getUnionType(), argumentsSet('oauth_company'));
    expectedReturnValues(\BusyPHP\oauth\model\info\MemberOauthInfo::getUnionType(), argumentsSet('oauth_type'));
    
}