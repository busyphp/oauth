<?php

namespace BusyPHP\oauth\model;

use BusyPHP\exception\AppException;
use BusyPHP\exception\ClassNotFoundException;
use BusyPHP\exception\ClassNotImplementsException;
use BusyPHP\exception\ParamInvalidException;
use BusyPHP\exception\SQLException;
use BusyPHP\exception\VerifyException;
use BusyPHP\Model;
use BusyPHP\oauth\interfaces\OAuth;
use BusyPHP\oauth\interfaces\OAuthApp;
use BusyPHP\oauth\interfaces\OAuthAppData;
use BusyPHP\oauth\interfaces\OAuthModel;
use BusyPHP\oauth\interfaces\OnOAuthBindOrRegisterCallback;
use BusyPHP\oauth\model\info\MemberOauthInfo;
use BusyPHP\oauth\model\info\OAuthLoginInfo;
use Exception;


/**
 * 三方登录模型
 * @author busy^life <busy.life@qq.com>
 * @copyright 2015 - 2017 busy^life <busy.life@qq.com>
 * @version $Id: 2017-12-16 下午6:11 MemberOauth.php busy^life $
 * @method MemberOauthInfo findInfo($pkValue = null, $emptyMessage = '')
 * @method MemberOauthInfo[] selectList()
 */
class MemberOauth extends Model
{
    public function __construct()
    {
        parent::__construct();
        
        $this->app->config->load($this->app->getRootPath() . 'config' . DIRECTORY_SEPARATOR . 'extend' . DIRECTORY_SEPARATOR . 'oauth.php', 'oauth');
    }
    
    
    /**
     * 获取配置
     * @param string $name 配置名称
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function getConfigure($name, $default = null)
    {
        return $this->app->config->get('oauth.' . $name, $default);
    }
    
    
    /**
     * 获取信息
     * @param int $id
     * @return array|MemberOauthInfo
     * @throws SQLException
     */
    public function getInfo($id)
    {
        return parent::getInfo(intval($id), '绑定记录不存在');
    }
    
    
    /**
     * 插入OAuth数据
     * @param MemberOauthField $insert
     * @return int|false 返回false代表已被其他人绑定，返回ini代表绑定成功的记录ID
     * @throws ParamInvalidException
     * @throws SQLException
     */
    protected function insertData(MemberOauthField $insert)
    {
        if ($insert->userId < 1) {
            throw new ParamInvalidException('user_id');
        }
        
        if (!$insert->unionid && !$insert->openid) {
            throw new ParamInvalidException('openid,unionid');
        }
        
        if ($insert->type < 1 || $insert->unionType < 1) {
            throw new ParamInvalidException('type, union_type');
        }
        
        // 检测会员是否绑定过
        if ($info = $this->checkBindByUserId($insert->userId, $insert->type, $insert->unionType)) {
            // openid一致则认为是自己
            // 否则已被别人绑定
            if ($info->openid == $insert->openid) {
                return $info->id;
            }
            
            return false;
        }
        
        
        $insert->createTime = time();
        $insert->updateTime = time();
        if (!$insertId = $this->addData($insert)) {
            throw new SQLException('插入绑定记录失败', $this);
        }
        
        return $insertId;
    }
    
    
    /**
     * 获取Oauth接口类名
     * @param int $type 登录方式
     * @return OAuth
     * @throws AppException
     */
    protected function getOAuthClassName($type)
    {
        $type  = intval($type);
        $types = $this->getConfigure('types', []);
        if (!$types || !isset($types[$type])) {
            throw new AppException("不支持该登录方式");
        }
        
        $class = $types[$type]['class'];
        if (!$class || !class_exists($class)) {
            throw new AppException("该支付方式未指定登录接口类");
        }
        
        $OAuthParentClassName = OAuth::class;
        if (!is_subclass_of($class, OAuth::class)) {
            throw new AppException("登录接口类[{$class}]必须集成[{$OAuthParentClassName}]接口");
        }
        
        return $class;
    }
    
    
    /**
     * 获取会员关联模型对象
     * @return OAuthModel
     * @throws ClassNotFoundException
     * @throws ClassNotImplementsException
     */
    protected function getOAuthModel()
    {
        $memberClass = $this->getConfigure('member', '');
        if (!$memberClass || !class_exists($memberClass)) {
            throw new ClassNotFoundException($memberClass);
        }
        
        if (!is_subclass_of($memberClass, OAuthModel::class)) {
            throw new ClassNotImplementsException($memberClass, OAuthModel::class);
        }
        
        return call_user_func([$memberClass, 'init']);
    }
    
    
    /**
     * 获取APP登录OAuth对象
     * @param int          $type 登录方式
     * @param OAuthAppData $data 登录数据
     * @return OAuthApp
     * @throws AppException
     */
    public function getOAuthApp(int $type, OAuthAppData $data)
    {
        $class = $this->getOAuthClassName($type);
        
        return new $class($data);
    }
    
    
    /**
     * 通过OAuth数据和用户ID绑定
     * @param OAuth $oauth
     * @param int   $userId
     * @return MemberOauthInfo
     * @throws ParamInvalidException
     * @throws SQLException
     * @throws VerifyException
     */
    protected function bindByOAuthAndUserId(OAuth $oauth, $userId)
    {
        if ($userId < 1) {
            throw new ParamInvalidException('user_id');
        }
        
        
        $oauthInfo         = $oauth->onGetInfo();
        $insert            = MemberOauthField::init();
        $insert->userId    = $userId;
        $insert->type      = $oauthInfo->getType();
        $insert->unionType = $oauthInfo->getUnionType();
        $insert->unionid   = $oauthInfo->getUnionId();
        $insert->openid    = $oauthInfo->getOpenId();
        $insert->nickname  = $oauthInfo->getNickname();
        $insert->sex       = $oauthInfo->getSex();
        $insert->avatar    = $oauthInfo->getAvatar();
        $insert->userInfo  = json_encode($oauthInfo->getUserInfo());
        
        if (false === $insertId = $this->insertData($insert)) {
            throw new VerifyException('该账户已被他人绑定', 'repeat');
        }
        
        return $this->getInfo($insertId);
    }
    
    
    /**
     * 通过OAuth数据进行绑定
     * @param OAuth $oauth
     * @return MemberOauthInfo|false 返回false代表没有绑定
     * @throws ParamInvalidException
     * @throws SQLException
     * @throws VerifyException
     */
    protected function bindByOAuth(OAuth $oauth)
    {
        $oauthInfo = $oauth->onGetInfo();
        if ($info = $this->checkBindByOpenid($oauthInfo->getOpenId(), $oauthInfo->getType())) {
            return $info;
        }
        
        // 是否在其他的客户端绑定过
        if ($oauthInfo->getUnionId() && false !== $unionInfo = $this->checkBindByUnionId($oauthInfo->getUnionId(), $oauthInfo->getUnionType())) {
            return $this->bindByOAuthAndUserId($oauth, $unionInfo->userId);
        }
        
        return false;
    }
    
    
    /**
     * 通过OAuth数据和注册数据进行绑定
     * @param OAuth                         $oauth
     * @param OnOAuthBindOrRegisterCallback $callback
     * @return MemberOauthInfo
     * @throws Exception
     */
    public function bindByOAuthOrRegister(OAuth $oauth, OnOAuthBindOrRegisterCallback $callback) : MemberOauthInfo
    {
        // 执行绑定
        // 1. 用户已经在同厂商不通客户端登录，如：已经在公众号绑定，没有在app上绑定
        // 2. 用户从未登录过
        $info = $this->bindByOAuth($oauth);
        
        
        $this->startTrans();
        try {
            $memberModel = $this->getOAuthModel();
            
            // 没有绑定，两种情况
            // 1. 用户未注册过，则肯定没有绑定
            // 2. 用户已注册过，但是没有绑定任何记录
            if (false === $info) {
                // 执行查重回调
                $userId = $callback->onCheckRegisterRepeat();
                
                // 代表用户已注册，则直接为其绑定
                if ($userId > 0) {
                    $info = $this->bindByOAuthAndUserId($oauth, $userId);
                } else {
                    $userId = $memberModel->onOAuthRegister($callback->onGetRegisterField());
                    if ($userId < 1) {
                        throw new AppException('onGetRegisterField方法必须返回有效的会员ID');
                    }
                    
                    $info = $this->bindByOAuthAndUserId($oauth, $userId);
                }
            } else {
                // 有数据则更新
                $field = $callback->onGetUpdateField();
                if ($field->getDBData()) {
                    $memberModel->onOAuthUpdate($info, $field);
                }
            }
            
            $this->commit();
            
            return $info;
        } catch (Exception $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 执行登录
     * @param OAuth                         $oauth
     * @param OnOAuthBindOrRegisterCallback $callback
     * @return OAuthLoginInfo
     * @throws Exception
     */
    public function login(OAuth $oauth, OnOAuthBindOrRegisterCallback $callback) : OAuthLoginInfo
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
            $info = $this->lock(true)->checkBindByOpenid($apiInfo->getOpenId(), $apiInfo->getType());
            
            // 绑定记录不存在则需要绑定
            if (!$info) {
                $info = $this->bindByOAuthOrRegister($oauth, $callback);
            }
            
            // 执行会员登录
            $userInfo = $memberModal->onOAuthLogin($info, $apiInfo);
            
            // 更新绑定记录登录信息
            $save             = MemberOauthField::init();
            $save->nickname   = $apiInfo->getNickname();
            $save->avatar     = $apiInfo->getAvatar();
            $save->sex        = $apiInfo->setSex();
            $save->userInfo   = $apiInfo->getUserInfo();
            $save->lastIp     = $info->loginIp;
            $save->lastTime   = $info->loginTime;
            $save->loginIp    = $this->app->request->ip();
            $save->loginTime  = time();
            $save->loginTotal = $info->loginTotal + 1;
            if (false === $this->whereEnum(MemberOauthField::id($info->id))->saveData($save)) {
                throw new SQLException('更新绑定记录登录信息失败', $this);
            }
            
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
            
            $loginInfo            = new OAuthLoginInfo();
            $loginInfo->oauthInfo = $info;
            $loginInfo->modelInfo = $userInfo;
            
            return $loginInfo;
        } catch (Exception $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 通过Openid获取绑定记录
     * @param string $openid
     * @param int    $type 登录类型
     * @return MemberOauthInfo
     * @throws SQLException
     */
    public function getInfoByOpenId($openid, $type)
    {
        return $this->whereEnum(MemberOauthField::openid(trim($openid)))
            ->whereEnum(MemberOauthField::type(intval($type)))
            ->findInfo(null, '绑定记录不存在');
    }
    
    
    /**
     * 通过openid校验是否绑定
     * @param string $openid
     * @param int    $type 登录类型
     * @return false|MemberOauthInfo
     */
    public function checkBindByOpenid($openid, $type)
    {
        try {
            return $this->getInfoByOpenId($openid, $type);
        } catch (SQLException $e) {
            return false;
        }
    }
    
    
    /**
     * 通过UnionId获取绑定记录
     * @param string $unionId
     * @param int    $type 登录类型
     * @return MemberOauthInfo
     * @throws SQLException
     */
    public function getInfoByUnionId($unionId, $type)
    {
        return $this->whereEnum(MemberOauthField::unionid(trim($unionId)))
            ->whereEnum(MemberOauthField::unionType(intval($type)))
            ->findInfo(null, '绑定记录不存在');
    }
    
    
    /**
     * 通过unionId校验是否绑定
     * @param string $unionId
     * @param int    $type 登录类型
     * @return MemberOauthInfo|false
     */
    public function checkBindByUnionId($unionId, $type)
    {
        try {
            return $this->getInfoByUnionId($unionId, $type);
        } catch (SQLException $e) {
            return false;
        }
    }
    
    
    /**
     * 通过会员ID获取绑定信息
     * @param int $userId 会员ID
     * @param int $type 登录类型
     * @param int $unionType 绑定类型
     * @return MemberOauthInfo
     * @throws SQLException
     */
    public function getInfoByUserId($userId, int $type, int $unionType)
    {
        return $this->whereEnum(MemberOauthField::userId($userId))
            ->whereEnum(MemberOauthField::type($type))
            ->whereEnum(MemberOauthField::unionType($unionType))
            ->findInfo(null, 'Oauth记录不存在');
    }
    
    
    /**
     * 通过会员ID检测是否绑定
     * @param int $userId
     * @param int $type
     * @param int $unionType
     * @return MemberOauthInfo|bool
     */
    public function checkBindByUserId($userId, int $type, int $unionType)
    {
        try {
            return $this->getInfoByUserId($userId, $type, $unionType);
        } catch (SQLException $e) {
            return false;
        }
    }
}