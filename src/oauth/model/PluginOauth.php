<?php
declare(strict_types = 1);

namespace BusyPHP\oauth\model;

use BusyPHP\exception\ParamInvalidException;
use BusyPHP\Model;
use BusyPHP\model\Entity;
use BusyPHP\oauth\Driver;
use BusyPHP\oauth\exception\OAuthBindedException;
use BusyPHP\oauth\interfaces\OAuthInfo;
use BusyPHP\oauth\interfaces\OAuthUserModelInterface;
use BusyPHP\oauth\OAuthLoginResult;
use Closure;
use LogicException;
use think\Container;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\facade\Request;
use Throwable;

/**
 * 三方登录模型
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:33 PluginOauth.php $
 * @method PluginOauthField getInfo($data, $notFoundMessage = null)
 * @method PluginOauthField findInfo($data = null)
 * @method PluginOauthField[] selectList()
 * @method PluginOauthField[] indexList(string|Entity $key = 'id')
 * @method PluginOauthField[] indexListIn(array $range, string|Entity $key = 'id', string|Entity $field = 'id')
 */
class PluginOauth extends Model
{
    public           $name                = 'plugin_oauth';
    
    protected string $fieldClass          = PluginOauthField::class;
    
    protected string $dataNotFoundMessage = 'OAuth记录不存在';
    
    /**
     * 用户模型
     * @var OAuthUserModelInterface
     */
    protected OAuthUserModelInterface $userModel;
    
    
    /**
     * 插入OAuth数据
     * @param PluginOauthField $data
     * @return PluginOauthField
     * @throws DbException
     */
    protected function create(PluginOauthField $data) : PluginOauthField
    {
        if ($data->userId < 1) {
            throw new ParamInvalidException('$data->userId');
        }
        
        if (!$data->unionid && !$data->openid) {
            throw new ParamInvalidException('$data->unionid & $data->openid');
        }
        
        if ($data->type < 1 || $data->unionType < 1 || !$data->appId) {
            throw new ParamInvalidException('$data->type | $data->unionType | $data->appId');
        }
        
        // 检测会员是否绑定过
        if ($info = $this->where(PluginOauthField::userId($data->userId))
            ->where(PluginOauthField::type($data->type))
            ->where(PluginOauthField::unionType($data->unionType))
            ->where(PluginOauthField::appId($data->appId))
            ->findInfo()
        ) {
            // openid一致则认为是自己
            // 否则已被别人绑定
            if ($info->openid == $data->openid) {
                return $info;
            }
            
            throw new OAuthBindedException($info);
        }
        
        return $this->getInfo($this->insert($data));
    }
    
    
    /**
     * 设置用户模型
     * @param OAuthUserModelInterface $model
     * @return static
     */
    public function setUserModel(OAuthUserModelInterface $model) : static
    {
        $this->userModel = $model;
        
        return $this;
    }
    
    
    /**
     * 获取用户模型
     * @return OAuthUserModelInterface
     */
    public function getUserModel() : OAuthUserModelInterface
    {
        if (!isset($this->userModel)) {
            throw new LogicException('Must be set `OAuthUserModelInterface`');
        }
        
        return $this->userModel;
    }
    
    
    /**
     * 通过OAuth数据和用户ID绑定
     * @param OAuthInfo $auth 三方授权数据
     * @param int       $userId 绑定的用户ID
     * @return PluginOauthField
     * @throws DbException
     * @throws DataNotFoundException
     */
    protected function bindUserId(OAuthInfo $auth, int $userId) : PluginOauthField
    {
        if ($userId < 1) {
            throw new ParamInvalidException('$userId');
        }
        
        $insert            = PluginOauthField::init();
        $insert->userId    = $userId;
        $insert->type      = $auth->getType();
        $insert->unionType = $auth->getUnion();
        $insert->unionid   = $auth->getUnionId();
        $insert->openid    = $auth->getOpenId();
        $insert->appId     = $auth->getAppId();
        $insert->nickname  = $auth->getNickname();
        $insert->sex       = $auth->getSex();
        $insert->avatar    = $auth->getAvatar();
        $insert->userInfo  = $auth->getUserInfo();
        
        return $this->create($insert);
    }
    
    
    /**
     * 通过事务执行执行绑定或注册
     * @param OAuthInfo                      $auth 三方授权数据
     * @param Closure(OAuthInfo $auth):mixed $checkRegisterCallback 检查是否注册回调
     * @return PluginOauthField
     * @throws Throwable
     */
    public function bindOrRegisterForTransaction(OAuthInfo $auth, Closure $checkRegisterCallback) : PluginOauthField
    {
        return $this->transaction(function() use ($auth, $checkRegisterCallback) {
            return $this->bindOrRegister($auth, $checkRegisterCallback);
        });
    }
    
    
    /**
     * 执行绑定或注册
     * @param OAuthInfo                                                          $auth 三方授权数据
     * @param Closure(OAuthInfo $auth, OAuthUserModelInterface $userModel):mixed $checkRegisterCallback 检查是否注册回调
     * @return PluginOauthField
     * @throws Throwable
     */
    public function bindOrRegister(OAuthInfo $auth, Closure $checkRegisterCallback) : PluginOauthField
    {
        // 查询是否绑定过
        if ($info = $this->findInfoByOpenId($auth->getOpenId(), $auth->getType(), $auth->getAppId())) {
            return $info;
        }
        
        // 如果获取到了 unionId 则查询该用户是否在其他客户端绑定过
        // 如果在其他客户端绑定过，则使用该绑定记录新增绑定
        if ($auth->getUnionId() && $unionInfo = $this->findInfoByUnionId($auth->getUnionId(), $auth->getUnion())) {
            return $this->bindUserId($auth, $unionInfo->userId);
        }
        
        // 没有绑定，两种情况
        // 1. 用户未注册过，则肯定没有绑定
        // 2. 用户已注册过，但是没有绑定任何记录
        // 执行注册回调
        $userModel = $this->getUserModel();
        $result    = Container::getInstance()->invokeFunction($checkRegisterCallback, [$auth, $userModel]);
        $extend    = null;
        $userId    = 0;
        if (is_int($result)) {
            $userId = $result;
        } else {
            $extend = $result;
        }
        if ($userId < 1) {
            $userId = $userModel->onOAuthRegister($auth, $extend);
            if ($userId < 1) {
                throw new LogicException(sprintf('"%s::onOAuthRegister" must return the valid user id', get_debug_type($checkRegisterCallback)));
            }
        }
        
        return $this->bindUserId($auth, $userId);
    }
    
    
    /**
     * 执行登录
     * @param Driver                                                             $driver 登录驱动类
     * @param Closure(OAuthInfo $auth, OAuthUserModelInterface $userModel):mixed $checkRegisterCallback 检查是否注册回调
     * @return OAuthLoginResult
     * @throws Throwable
     */
    public function login(Driver $driver, Closure $checkRegisterCallback) : OAuthLoginResult
    {
        $auth = $driver->getInfo();
        if (!$auth->getOpenId() && !$auth->getUnionId()) {
            throw new ParamInvalidException('openid,unionId');
        }
        if ($auth->getType() < 1) {
            throw new ParamInvalidException('type');
        }
        
        $this->startTrans();
        try {
            // 绑定记录不存在则需要绑定
            if (
                !$info = $this->lock(true)->findInfoByOpenId(
                    $auth->getOpenId(),
                    $auth->getType(),
                    $auth->getAppId()
                )
            ) {
                $info = $this->bindOrRegister($auth, $checkRegisterCallback);
            }
            
            // 执行会员登录
            $userInfo = $this->getUserModel()
                ->onOAuthLogin($info, $auth, function(string $avatar) use ($driver) : bool {
                    return $driver->canUpdateAvatar($avatar);
                });
            
            // 更新绑定记录登录信息
            $save             = PluginOauthField::init();
            $save->nickname   = $auth->getNickname();
            $save->avatar     = $auth->getAvatar();
            $save->sex        = $auth->getSex();
            $save->userInfo   = $auth->getUserInfo();
            $save->lastIp     = $info->loginIp;
            $save->lastTime   = $info->loginTime;
            $save->loginIp    = Request::ip();
            $save->loginTime  = time();
            $save->loginTotal = $info->loginTotal + 1;
            
            // 补齐unionId
            if ($auth->getUnionId()) {
                $save->unionid = $auth->getUnionId();
            }
            
            $this->where(PluginOauthField::id($info->id))->update($save);
            $this->commit();
            
            $info                 = $this->getInfo($info->id);
            $loginInfo            = new OAuthLoginResult();
            $loginInfo->oauthInfo = $info;
            $loginInfo->userInfo  = $userInfo;
            
            return $loginInfo;
        } catch (Throwable $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 通过Openid获取绑定记录
     * @param string $openid OPENID
     * @param string $type 登录类型
     * @param string $appId 三方APPID
     * @return PluginOauthField|null
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function findInfoByOpenId(string $openid, string $type, string $appId) : ?PluginOauthField
    {
        return $this->where(PluginOauthField::openid(trim($openid)))
            ->where(PluginOauthField::type($type))
            ->where(PluginOauthField::appId($appId))
            ->findInfo();
    }
    
    
    /**
     * 通过UnionId获取绑定记录
     * @param string $unionId
     * @param string $type 登录类型
     * @return PluginOauthField|null
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function findInfoByUnionId(string $unionId, string $type) : ?PluginOauthField
    {
        return $this->where(PluginOauthField::unionid($unionId))
            ->where(PluginOauthField::unionType($type))
            ->findInfo();
    }
}