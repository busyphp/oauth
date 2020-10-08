<?php

namespace BusyPHP\oauth;

use BusyPHP\app\admin\model\admin\user\AdminUser;
use BusyPHP\Controller;
use BusyPHP\exception\AppException;

/**
 * 安装OAuth
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2019 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2020/10/7 下午5:41 下午 InstallController.php $
 */
class InstallController extends Controller
{
    public function index()
    {
        $sql = <<<SQL
CREATE TABLE `busy_member_oauth` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `type` SMALLINT(2) NOT NULL DEFAULT '0' COMMENT '登录类型',
  `union_type` SMALLINT(2) NOT NULL DEFAULT '0' COMMENT '厂商类型',
  `openid` VARCHAR(60) NOT NULL DEFAULT '' COMMENT 'openid',
  `unionid` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '同登录类型唯一值',
  `create_time` INT(11) NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `update_time` INT(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `login_total` INT(11) NOT NULL DEFAULT '0' COMMENT '登录次数',
  `login_ip` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '本次登录IP',
  `last_ip` VARCHAR(45) NOT NULL DEFAULT '' COMMENT '上次登录IP',
  `login_time` INT(11) NOT NULL DEFAULT '0' COMMENT '本次登录时间',
  `last_time` INT(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `nickname` VARCHAR(60) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `user_info` TEXT NOT NULL COMMENT '登录数据',
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`),
   KEY `type` (`type`),
   KEY `openid` (`openid`),
   KEY `unionid` (`unionid`),
   KEY `union_type` (`union_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='OAuth登录';
SQL;
        
        try {
            $db = AdminUser::init();
            if ($db->query("SELECT table_name FROM information_schema.TABLES where table_name='busy_member_oauth' AND table_schema='{$db->getConfig('database')}'")) {
                throw new AppException('您已安装过该插件，请勿重复安装');
            }
            
            $db->execute($sql);
            
            return $this->success('安装成功', '/');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), '/');
        }
    }
}