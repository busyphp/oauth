<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\app\controller;

use BusyPHP\app\admin\annotation\MenuNode;
use BusyPHP\app\admin\annotation\MenuRoute;
use BusyPHP\app\admin\component\js\driver\Table;
use BusyPHP\app\admin\controller\AdminController;
use BusyPHP\app\admin\model\system\config\SystemConfig;
use BusyPHP\app\admin\model\system\config\SystemConfigField;
use BusyPHP\app\admin\model\system\menu\SystemMenu;
use BusyPHP\facade\OAuth;
use BusyPHP\helper\FilterHelper;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\Response;
use Throwable;

/**
 * 三方登录参数管理
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/5/24 01:31 SettingController.php $
 */
#[MenuRoute(path: 'plugin_oauth', class: true)]
class SettingController extends AdminController
{
    protected function display($template = '', $charset = 'utf-8', $contentType = '', $content = '') : Response
    {
        $this->app->config->set([
            'view_path'   => __DIR__ . '/../view/',
            'view_depr'   => DIRECTORY_SEPARATOR,
            'view_suffix' => 'html',
            'auto_rule'   => 1
        ], 'view');
        
        return parent::display($template, $charset, $contentType, $content);
    }
    
    
    /**
     * @return Response
     * @throws Throwable
     */
    #[MenuNode(menu: false, name: '三方登录', parent: 'system_manager/index', sort: 200)]
    public function index() : Response
    {
        if ($table = Table::initIfRequest()) {
            $map      = OAuth::getConfig('drivers', []);
            if (!is_array($map)) {
                $map = [];
            }
            
            $list = [];
            foreach ($map as $id => $item) {
                $driver = OAuth::driver($id);
                $list[] = [
                    'id'    => $id,
                    'type'  => $driver->getType(),
                    'union' => $driver->getUnion(),
                    'name'  => $driver->getName()
                ];
            }
            
            return $table->list($list)->response();
        }
        
        $this->assign('nav', SystemMenu::init()->getChildList('system_manager/index', true, true));
        
        return $this->display();
    }
    
    
    /**
     * @throws DbException
     * @throws DataNotFoundException
     * @throws Throwable
     */
    #[MenuNode(menu: false, name: '配置参数', parent: '/index')]
    public function setting() : Response
    {
        $id         = $this->param('id/s', 'trim');
        $driver     = OAuth::driver($id);
        $settingKey = OAuth::getSettingKey($id);
        
        if ($this->isPost()) {
            $data = SystemConfigField::init();
            $data->setSystem(true);
            $data->setName(sprintf('三方登录参数配置 - %s', $id));
            $data->setContent(FilterHelper::trim($this->post('content/a')));
            SystemConfig::init()->setting($settingKey, $data);
            
            $this->log()->record(self::LOG_UPDATE, '配置三方登录参数');
            
            return $this->success('配置成功');
        }
        
        
        $this->assign('id', $id);
        $this->assign('content', SystemConfig::instance()->getSettingData($settingKey));
        $this->assign('form', $driver->getSettingForm());
        $this->assign('name', $driver->getName());
        
        return $this->display();
    }
}