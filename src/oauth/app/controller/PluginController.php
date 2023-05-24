<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\app\controller;

use BusyPHP\app\admin\controller\develop\plugin\SystemPluginBaseController;
use BusyPHP\app\admin\model\system\plugin\SystemPlugin;
use RuntimeException;
use think\Response;
use Throwable;

/**
 * 插件管理
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:24 PluginController.php $
 */
class PluginController extends SystemPluginBaseController
{
    /**
     * 创建表SQL
     * @var string
     */
    private string $createTableSql = <<<SQL
CREATE TABLE `#__table_prefix__#plugin_oauth` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '登录类型',
  `union_type` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '厂商类型',
  `openid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'openid',
  `unionid` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '同登录类型唯一值',
  `app_id` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '三方APPID',
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
  `user_info` JSON NULL COMMENT '登录数据',
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`),
   KEY `type` (`type`),
   KEY `openid` (`openid`),
   KEY `unionid` (`unionid`),
   KEY `app_id` (`app_id`),
   KEY `union_type` (`union_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='OAuth授权登录表'
SQL;
    
    
    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function install() : Response
    {
        $model = SystemPlugin::init();
        $model->startTrans();
        try {
            if ($this->hasTable('plugin_oauth')) {
                throw new RuntimeException('plugin_oauth数据表已存在');
            }
            
            $this->executeSQL($this->createTableSql);
            $model->setInstall($this->info->package);
            $model->commit();
        } catch (Throwable $e) {
            $model->rollback();
            
            throw $e;
        }
        
        $this->updateCache();
        $this->logInstall();
        
        return $this->success('安装成功');
    }
    
    
    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function uninstall() : Response
    {
        throw new RuntimeException('不支持卸载');
    }
    
    
    /**
     * @inheritDoc
     */
    protected function viewPath() : string
    {
        return '';
    }
    
    
    /**
     * @inheritDoc
     */
    public function setting() : Response
    {
        return response();
    }
}