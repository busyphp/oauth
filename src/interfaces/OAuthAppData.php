<?php

namespace BusyPHP\oauth\interfaces;

/**
 * OAuthAPP登录基本数据接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/8 下午12:45 下午 OAuthAppData.php $
 */
abstract class OAuthAppData
{
    /**
     * 获取数据
     * @return mixed
     */
    abstract function getData();
}