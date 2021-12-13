<?php

namespace BusyPHP\oauth\model;

use BusyPHP\App;
use BusyPHP\exception\ClassNotFoundException;
use BusyPHP\exception\ClassNotImplementsException;
use BusyPHP\exception\ParamInvalidException;
use BusyPHP\exception\VerifyException;
use BusyPHP\Model;
use BusyPHP\oauth\interfaces\OAuth;
use BusyPHP\oauth\interfaces\OAuthApp;
use BusyPHP\oauth\interfaces\OAuthAppData;
use BusyPHP\oauth\interfaces\OAuthModel;
use BusyPHP\oauth\interfaces\OAuthCallback;
use RuntimeException;
use think\Container;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use Throwable;

/**
 * 三方登录模型
 * @author busy^life <busy.life@qq.com>
 * @copyright (c) 2015--2021 ShanXi Han Tuo Technology Co.,Ltd. All rights reserved.
 * @version $Id: 2021/11/10 下午10:33 MemberOauth.php $
 * @method MemberOauthInfo getInfo($data, $notFoundMessage = null)
 * @method MemberOauthInfo findInfo($data = null, $notFoundMessage = null)
 * @method MemberOauthInfo[] selectList()
 */
class MemberOauth extends Model
{
    protected $bindParseClass      = MemberOauthInfo::class;
    
    protected $dataNotFoundMessage = '绑定记录不存在';
    
    
    /**
     * 获取配置
     * @param string $name 配置名称
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function getOauthConfig($name, $default = null)
    {
        $app = App::getInstance();
        if (!$app->config->get('oauth')) {
            $app->config->load($app->getRootPath() . 'config' . DIRECTORY_SEPARATOR . 'extend' . DIRECTORY_SEPARATOR . 'oauth.php', 'oauth');
        }
        
        return $app->config->get('oauth.' . $name, $default);
    }
    
    
    /**
     * 插入OAuth数据
     * @param MemberOauthField $insert
     * @return int|false 返回false代表已被其他人绑定，返回ini代表绑定成功的记录ID
     * @throws DbException
     */
    protected function createOAuth(MemberOauthField $insert)
    {
        if ($insert->userId < 1) {
            throw new ParamInvalidException('user_id');
        }
        
        if (!$insert->unionid && !$insert->openid) {
            throw new ParamInvalidException('openid,unionid');
        }
        
        if ($insert->type < 1 || $insert->unionType < 1 || !$insert->appId) {
            throw new ParamInvalidException('type, union_type');
        }
        
        // 检测会员是否绑定过
        if ($info = $this->whereEntity(MemberOauthField::userId($insert->userId))
            ->whereEntity(MemberOauthField::type($insert->type))
            ->whereEntity(MemberOauthField::unionType($insert->unionType))
            ->whereEntity(MemberOauthField::appId($insert->appId))
            ->findInfo()) {
            // openid一致则认为是自己
            // 否则已被别人绑定
            if ($info->openid == $insert->openid) {
                return $info->id;
            }
            
            return false;
        }
        
        
        $insert->createTime = time();
        $insert->updateTime = time();
        
        return $this->addData($insert);
    }
    
    
    /**
     * 获取Oauth接口类名
     * @param int $type 登录方式
     * @return string
     */
    protected function getOAuthClassName(int $type) : string
    {
        $types = $this->getOauthConfig('types', []);
        if (!$types || !isset($types[$type])) {
            throw new RuntimeException("不支持该登录方式");
        }
        
        $class = $types[$type]['class'];
        if (!$class) {
            throw new RuntimeException('未绑定登录接口类');
        }
        
        if (!class_exists($class)) {
            throw new ClassNotFoundException($class);
        }
        
        if (!is_subclass_of($class, OAuth::class)) {
            throw new ClassNotImplementsException($class, OAuth::class);
        }
        
        return $class;
    }
    
    
    /**
     * 获取会员关联模型对象
     * @return OAuthModel
     */
    protected function getOAuthModel() : OAuthModel
    {
        $impl = $this->getOauthConfig('member', '');
        if (!$impl) {
            throw new RuntimeException('未关联会员模型');
        }
        
        $model = Container::getInstance()->make($impl, [], true);
        if (!$model instanceof OAuthModel) {
            throw new ClassNotImplementsException($impl, OAuthModel::class);
        }
        
        return $model;
    }
    
    
    /**
     * 获取APP登录OAuth对象
     * @param int          $type 登录方式
     * @param OAuthAppData $data 登录数据
     * @return OAuthApp
     */
    public function getOAuthApp(int $type, OAuthAppData $data) : OAuthApp
    {
        $class = $this->getOAuthClassName($type);
        
        return new $class($data);
    }
    
    
    /**
     * 通过OAuth数据和用户ID绑定
     * @param OAuth $oauth
     * @param int   $userId
     * @return MemberOauthInfo
     * @throws DbException
     * @throws DataNotFoundException
     */
    protected function bindByOAuthAndUserId(OAuth $oauth, $userId) : MemberOauthInfo
    {
        if ($userId < 1) {
            throw new ParamInvalidException('$userId');
        }
        
        $oauthInfo         = $oauth->onGetInfo();
        $insert            = MemberOauthField::init();
        $insert->userId    = $userId;
        $insert->type      = $oauthInfo->getType();
        $insert->unionType = $oauthInfo->getUnionType();
        $insert->unionid   = $oauthInfo->getUnionId();
        $insert->openid    = $oauthInfo->getOpenId();
        $insert->appId     = $oauthInfo->getAppId();
        $insert->nickname  = $oauthInfo->getNickname();
        $insert->sex       = $oauthInfo->getSex();
        $insert->avatar    = $oauthInfo->getAvatar();
        $insert->userInfo  = json_encode($oauthInfo->getUserInfo());
        
        if (false === $insertId = $this->createOAuth($insert)) {
            throw new VerifyException('该账户已被他人绑定', 'repeat');
        }
        
        return $this->getInfo($insertId);
    }
    
    
    /**
     * 通过OAuth数据进行绑定
     * @param OAuth $oauth
     * @return MemberOauthInfo 返回null代表没有绑定
     * @throws DataNotFoundException
     * @throws DbException
     */
    protected function bindByOAuth(OAuth $oauth) : ?MemberOauthInfo
    {
        $oauthInfo = $oauth->onGetInfo();
        if ($info = $this->getInfoByOpenId($oauthInfo->getOpenId(), $oauthInfo->getType(), $oauthInfo->getAppId())) {
            return $info;
        }
        
        // 是否在其他的客户端绑定过
        if ($oauthInfo->getUnionId() && !$unionInfo = $this->getInfoByUnionId($oauthInfo->getUnionId(), $oauthInfo->getUnionType())) {
            return $this->bindByOAuthAndUserId($oauth, $unionInfo->userId);
        }
        
        return null;
    }
    
    
    /**
     * 通过OAuth数据和注册数据进行绑定
     * @param OAuth         $oauth 三方登录接口
     * @param OAuthCallback $callback 回调
     * @param bool          $disabledTrans 是否禁用事物
     * @return MemberOauthInfo
     * @throws Throwable
     */
    public function bindByOAuthOrRegister(OAuth $oauth, OAuthCallback $callback, $disabledTrans = false) : MemberOauthInfo
    {
        // 执行绑定
        // 1. 用户已经在同厂商不通客户端登录，如：已经在公众号绑定，没有在app上绑定
        // 2. 用户从未登录过
        $info = $this->bindByOAuth($oauth);
        
        
        $this->startTrans($disabledTrans);
        try {
            $memberModel = $this->getOAuthModel();
            
            // 没有绑定，两种情况
            // 1. 用户未注册过，则肯定没有绑定
            // 2. 用户已注册过，但是没有绑定任何记录
            if (!$info) {
                // 执行查重回调
                $userId = $callback->onCheckRegister($oauth);
                
                // 代表用户已注册，则直接为其绑定
                if ($userId > 0) {
                    $info = $this->bindByOAuthAndUserId($oauth, $userId);
                } else {
                    $userId = $memberModel->onOAuthRegister($callback->onGetRegisterData($oauth));
                    if ($userId < 1) {
                        throw new RuntimeException('onGetRegisterField方法必须返回有效的会员ID');
                    }
                    
                    $info = $this->bindByOAuthAndUserId($oauth, $userId);
                }
            }
            
            $this->commit($disabledTrans);
            
            return $info;
        } catch (Throwable $e) {
            $this->rollback($disabledTrans);
            
            throw $e;
        }
    }
    
    
    /**
     * 执行登录
     * @param OAuth         $oauth
     * @param OAuthCallback $callback
     * @return MemberOAuthLoginResult
     * @throws Throwable
     */
    public function login(OAuth $oauth, OAuthCallback $callback) : MemberOAuthLoginResult
    {
        $apiInfo = $oauth->onGetInfo();
        if (!$apiInfo->getOpenId() && !$apiInfo->getUnionId()) {
            throw new ParamInvalidException('openid,unionId');
        }
        if ($apiInfo->getType() < 1) {
            throw new ParamInvalidException('type');
        }
        
        $memberModal = $this->getOAuthModel();
        
        $this->startTrans();
        try {
            $info = $this->lock(true)
                ->getInfoByOpenId($apiInfo->getOpenId(), $apiInfo->getType(), $apiInfo->getAppId());
            
            // 绑定记录不存在则需要绑定
            if (!$info) {
                $info = $this->bindByOAuthOrRegister($oauth, $callback, true);
            }
            
            // 执行会员登录
            $userInfo = $memberModal->onOAuthLogin($info, $oauth);
            
            // 更新绑定记录登录信息
            $save             = MemberOauthField::init();
            $save->nickname   = $apiInfo->getNickname();
            $save->avatar     = $apiInfo->getAvatar();
            $save->sex        = $apiInfo->setSex();
            $save->userInfo   = $apiInfo->getUserInfo();
            $save->lastIp     = $info->loginIp;
            $save->lastTime   = $info->loginTime;
            $save->loginIp    = App::getInstance()->request->ip();
            $save->loginTime  = time();
            $save->loginTotal = $info->loginTotal + 1;
            $this->whereEntity(MemberOauthField::id($info->id))->saveData($save);
            
            $info->nickname   = $save->nickname;
            $info->avatar     = $save->avatar;
            $info->sex        = $save->sex;
            $info->userInfo   = $save->userInfo;
            $info->lastIp     = $save->lastIp;
            $info->lastTime   = $save->lastTime;
            $info->loginIp    = $save->loginIp;
            $info->loginTime  = $save->loginTime;
            $info->loginTotal = $save->loginTotal;
            
            $this->commit();
            
            $loginInfo            = new MemberOAuthLoginResult();
            $loginInfo->oauthInfo = $info;
            $loginInfo->modelInfo = $userInfo;
            
            return $loginInfo;
        } catch (Throwable $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 通过Openid获取绑定记录
     * @param string $openid OPENID
     * @param int    $type 登录类型
     * @param string $appId 三方APPID
     * @return MemberOauthInfo
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function getInfoByOpenId(string $openid, int $type, string $appId) : ?MemberOauthInfo
    {
        return $this->whereEntity(MemberOauthField::openid(trim($openid)))
            ->whereEntity(MemberOauthField::type(intval($type)))
            ->whereEntity(MemberOauthField::appId($appId))
            ->findInfo();
    }
    
    
    /**
     * 通过UnionId获取绑定记录
     * @param string $unionId
     * @param int    $type 登录类型
     * @return MemberOauthInfo|null
     * @throws DataNotFoundException
     * @throws DbException
     */
    public function getInfoByUnionId($unionId, $type) : ?MemberOauthInfo
    {
        return $this->whereEntity(MemberOauthField::unionid(trim($unionId)))
            ->whereEntity(MemberOauthField::unionType(intval($type)))
            ->findInfo();
    }
}