<?php
declare(strict_types = 1);

namespace BusyPHP;

use BusyPHP\app\admin\model\system\config\SystemConfig;
use BusyPHP\helper\ArrayHelper;
use BusyPHP\helper\StringHelper;
use BusyPHP\oauth\Driver;
use InvalidArgumentException;
use think\Manager;

/**
 * OAuth
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/5/23 18:12 OAuth.php $
 * @mixin Driver
 */
class OAuth extends Manager
{
    protected $namespace = '\\BusyPHP\\oauth\\driver\\';
    
    
    /**
     * 获取指定驱动
     * @param string|null $name
     */
    public function driver(string $name = null) : Driver
    {
        return parent::driver($name);
    }
    
    
    /**
     * 获取OAuth配置
     * @access public
     * @param null|string $name 名称
     * @param mixed|null  $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = null, mixed $default = null) : mixed
    {
        if (null !== $name) {
            return $this->app->config->get('oauth.' . $name, $default);
        }
        
        return $this->app->config->get('oauth');
    }
    
    
    /**
     * 获取OAuth驱动配置
     * @param string      $driver
     * @param string|null $name
     * @param mixed       $default
     * @return array
     */
    public function getDriverConfig(string $driver, string $name = null, mixed $default = null) : mixed
    {
        if ($config = $this->getConfig('drivers.' . $driver)) {
            return ArrayHelper::get(
                array_merge(
                    $config,
                    SystemConfig::init()->getSettingData($this->getSettingKey($driver))
                ),
                $name,
                $default
            );
        }
        
        throw new InvalidArgumentException("Driver [$driver] not found.");
    }
    
    
    /**
     * 获取设置Key
     * @param string $driver
     * @return string
     */
    public function getSettingKey(string $driver) : string
    {
        return 'plugin_oauth_' . StringHelper::snake($driver);
    }
    
    
    protected function resolveType(string $name)
    {
        if ($class = $this->getDriverConfig($name, 'class')) {
            return $class;
        }
        
        return $this->getDriverConfig($name, 'type');
    }
    
    
    protected function resolveConfig(string $name)
    {
        return $this->getDriverConfig($name);
    }
    
    
    public function getDefaultDriver()
    {
        return $this->getConfig('default');
    }
}