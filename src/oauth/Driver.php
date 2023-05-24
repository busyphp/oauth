<?php
declare(strict_types = 1);

namespace BusyPHP\oauth;

use BusyPHP\App;
use BusyPHP\facade\OAuth;
use BusyPHP\oauth\interfaces\OAuthDataInterface;
use BusyPHP\oauth\interfaces\OAuthInfo;
use BusyPHP\oauth\interfaces\OAuthUserModelInterface;
use BusyPHP\oauth\model\PluginOauth;
use Closure;
use RuntimeException;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use Throwable;

/**
 * OAuth驱动基本类
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2023 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2023/5/23 16:19 Driver.php $
 */
abstract class Driver
{
    /**
     * 配置参数
     * @var array
     */
    protected array $config = [
        // 必须，驱动类型(登录类型)，如：wechat_mini，如果不设置class则默认使用 BusyPHP\oauth\driver\WechatMini 作为驱动类
        'type'       => '',
        
        // 可选，驱动类，设置后驱动类按照该类
        'class'      => '',
        
        // 必须，联合类型，如微信登录包含 小程序、APP、公众号等不同的登录方式，为了让账号互通，就需要将这些驱动归为一个分组
        'union'      => '',
        
        // 必须，登录类型名称
        'name'       => '',
        
        // 必须，对应厂商提供的appId
        'app_id'     => '',
        
        // 对应厂商提供的秘钥
        'app_secret' => ''
    ];
    
    /**
     * AccessToken
     * @var string
     */
    protected string $accessToken;
    
    /**
     * @var OAuthInfo
     */
    protected OAuthInfo $authInfo;
    
    /**
     * 登录方式名称
     * @var string
     */
    protected string $name;
    
    /**
     * 获取登录类型
     * @var string
     */
    protected string $type;
    
    /**
     * 获取厂商类型
     * @var string
     */
    protected string $union;
    
    /**
     * AppID
     * @var string
     */
    protected string $appId;
    
    /**
     * AppSecret
     * @var string
     */
    protected string $appSecret;
    
    /**
     * 登录数据
     * @var OAuthDataInterface
     */
    protected OAuthDataInterface $data;
    
    
    /**
     * 构造函数
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config    = array_merge($this->config, $config);
        $this->name      = (string) ($this->config['name'] ?? '');
        $this->union     = (string) ($this->config['union'] ?? '');
        $this->type      = (string) ($this->config['type'] ?? '');
        $this->appId     = $this->config['app_id'];
        $this->appSecret = $this->config['app_secret'];
    }
    
    
    /**
     * 获取登录类型名称
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    
    /**
     * 获取登录类型
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }
    
    
    /**
     * 获取联合类型
     * @return string
     */
    public function getUnion() : string
    {
        return $this->union;
    }
    
    
    /**
     * 获取三方APPID
     * @return string
     */
    public function getAppId() : string
    {
        return $this->appId;
    }
    
    
    /**
     * 获取通信票据
     * @return string
     */
    protected function getAccessToken() : string
    {
        if (!isset($this->accessToken)) {
            $this->accessToken = $this->onGetAccessToken();
        }
        
        return $this->accessToken;
    }
    
    
    /**
     * 设置登录数据
     * @param OAuthDataInterface $data
     * @return static
     */
    public function setData(OAuthDataInterface $data) : static
    {
        $this->data = $data;
        
        return $this;
    }
    
    
    /**
     * 获取授权的用户信息
     * @return OAuthInfo
     */
    public function getInfo() : OAuthInfo
    {
        if (!isset($this->authInfo)) {
            $this->authInfo = $this->onGetInfo();
        }
        
        return $this->authInfo;
    }
    
    
    /**
     * 普通登录
     * @param OAuthUserModelInterface                                            $userModel 用户模型
     * @param Closure(OAuthInfo $auth, OAuthUserModelInterface $userModel):mixed $checkRegisterCallback 检测是否注册回调
     * @return OAuthLoginResult
     * @throws Throwable
     */
    public function login(OAuthUserModelInterface $userModel, Closure $checkRegisterCallback) : OAuthLoginResult
    {
        return PluginOauth::init()->setUserModel($userModel)->login($this, $checkRegisterCallback);
    }
    
    
    /**
     * 浏览器跳转登录
     * @param string                                                             $redirectUri 回调地址
     * @param OAuthUserModelInterface                                            $userModel 用户模型
     * @param Closure(OAuthInfo $auth, OAuthUserModelInterface $userModel):mixed $checkRegisterCallback 检测是否注册回调
     * @return OAuthLoginResult
     * @throws Throwable
     */
    public function webLogin(string $redirectUri, OAuthUserModelInterface $userModel, Closure $checkRegisterCallback) : OAuthLoginResult
    {
        if (!$this->isApplyAuthRedirected(App::getInstance()->request)) {
            throw new HttpResponseException(Response::create($this->onGetApplyAuthUrl($redirectUri), 'redirect', 302));
        } else {
            return PluginOauth::init()->setUserModel($userModel)->login($this, $checkRegisterCallback);
        }
    }
    
    
    /**
     * 获取授权申请URL
     * @param string $redirectUri 回调地址
     */
    public function onGetApplyAuthUrl(string $redirectUri) : string
    {
        throw new RuntimeException(sprintf('必须实现 "%s::onGetApplyAuthUrl()" 方法', get_debug_type($this)));
    }
    
    
    /**
     * 是否已从授权URL跳转回来
     * @param Request $request
     * @return bool
     */
    protected function isApplyAuthRedirected(Request $request) : bool
    {
        return false;
    }
    
    
    /**
     * 获取设置表单配置
     * @return array
     */
    public function getSettingForm() : array
    {
        return [
            [
                // 表单名称
                'label'       => 'AppID',
                
                // 表单类型，支持：input, textarea, select
                'tag'         => 'input',
                
                // input 类型，支持：text,password,email,number等
                'type'        => 'text',
                
                // select 选项
                // 'options' => [
                //    ['value' => '选项值', 'text' => '选项名称']
                //],
                
                // textarea行高
                // 'rows' => 4,
                
                // name
                'name'        => 'app_id',
                
                // 是否必填
                'required'    => true,
                
                // placeholder
                'placeholder' => '请输入AppID',
                
                // 辅助文案
                'help'        => '设置AppID',
                
                // 自定义属性
                'attributes'  => [
                    'data-msg-required' => '请输入AppID'
                ]
            ],
            [
                'label'       => 'AppSecret',
                'tag'         => 'input',
                'type'        => 'text',
                'name'        => 'app_secret',
                'required'    => true,
                'placeholder' => '请输入AppSecret',
                'help'        => '设置AppSecret',
                'attributes'  => [
                    'data-msg-required' => '请输入AppSecret'
                ]
            ]
        ];
    }
    
    
    /**
     * 执行获取票据
     * @return string
     */
    abstract protected function onGetAccessToken() : string;
    
    
    /**
     * 执行获取授权信息
     * @return OAuthInfo
     */
    abstract protected function onGetInfo() : OAuthInfo;
    
    
    /**
     * 是否可以更新头像，一般用于在登录的时判断用户在三方账户上已经更新头像，目前系统中还是保存的旧头像
     * @param string $avatar 用户已设置的头像地址
     * @return bool
     */
    abstract public function canUpdateAvatar(string $avatar) : bool;
}