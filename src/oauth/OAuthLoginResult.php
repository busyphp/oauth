<?php
declare(strict_types = 1);

namespace BusyPHP\oauth;

use BusyPHP\model\Field;
use BusyPHP\model\ObjectOption;
use BusyPHP\oauth\model\PluginOauthField;

/**
 * OAuth登录返回信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:52 LoginResult.php $
 * @property Field            $userInfo 用户模型数据
 * @property PluginOauthField $oauthInfo OAuth模型绑定数据
 */
class OAuthLoginResult extends ObjectOption
{
}