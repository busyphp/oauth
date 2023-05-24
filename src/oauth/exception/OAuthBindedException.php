<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\exception;

use BusyPHP\oauth\model\PluginOauthField;
use RuntimeException;

/**
 * 已被其他用户绑定异常类
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/5/23 12:27 OAuthBindedException.php $
 */
class OAuthBindedException extends RuntimeException
{
    protected PluginOauthField $info;
    
    
    public function __construct(PluginOauthField $info)
    {
        parent::__construct('该账户已被他人绑定');
        
        $this->info = $info;
    }
    
    
    /**
     * @return PluginOauthField
     */
    public function getInfo() : PluginOauthField
    {
        return $this->info;
    }
}