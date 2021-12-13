<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\model;

use BusyPHP\model\Field;
use BusyPHP\model\ObjectOption;

/**
 * OAuth登录返回信息结构
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:52 OAuthLoginInfo.php $
 * @property Field           $modelInfo 会员模型数据
 * @property MemberOauthInfo $oauthInfo OAuth模型绑定数据
 */
class MemberOAuthLoginResult extends ObjectOption
{
}