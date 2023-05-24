<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\interfaces;

use BusyPHP\Model;
use BusyPHP\model\Field;
use BusyPHP\oauth\model\PluginOauthField;
use Closure;
use Throwable;

/**
 * OAuth用户模型接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:12 OAuthUserModelInterface.php $
 * @mixin Model
 */
interface OAuthUserModelInterface
{
    /**
     * 执行OAuth注册，不要使用事务
     * @param OAuthInfo $auth 三方授权数据
     * @param mixed     $extend 扩展的注册数据
     * @return int 注册后的用户ID
     * @throws Throwable
     */
    public function onOAuthRegister(OAuthInfo $auth, mixed $extend) : int;
    
    
    /**
     * 执行OAuth登录，不要使用事务
     * @param PluginOauthField             $oauthInfo 绑定记录数据
     * @param OAuthInfo                    $auth 三方授权数据
     * @param Closure(string $avatar):bool $canUpdateAvatar 验证是否可以更新头像回调
     * @return array|Field
     */
    public function onOAuthLogin(PluginOauthField $oauthInfo, OAuthInfo $auth, Closure $canUpdateAvatar) : array|Field;
}