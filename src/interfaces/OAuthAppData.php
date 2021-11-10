<?php

namespace BusyPHP\oauth\interfaces;

/**
 * OAuthAPP登录基本数据接口
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午11:12 OAuthAppData.php $
 */
abstract class OAuthAppData
{
    /**
     * 获取数据
     * @return mixed
     */
    abstract function getData();
}