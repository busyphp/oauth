<?php

namespace BusyPHP\oauth\model;

use BusyPHP\exception\AppException;
use BusyPHP\exception\ParamInvalidException;
use BusyPHP\exception\SQLException;
use BusyPHP\exception\VerifyException;
use BusyPHP\Model;
use BusyPHP\model\Field;
use BusyPHP\oauth\interfaces\OAuth;
use BusyPHP\oauth\interfaces\OAuthApp;
use BusyPHP\oauth\interfaces\OAuthAppData;
use BusyPHP\oauth\interfaces\OAuthModel;
use Exception;


/**
 * 三方登录模型
 * @author busy^life <busy.life@qq.com>
 * @copyright 2015 - 2017 busy^life <busy.life@qq.com>
 * @version $Id: 2017-12-16 下午6:11 MemberOauth.php busy^life $
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
     * @return array
     * @throws SQLException
     */
    public function getInfo($id)
    {
        return parent::getInfo($id, '绑定记录不存在');
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
            if ($info['openid'] == $insert->openid) {
                return $info['id'];
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
     * @throws AppException
     */
    protected function getOAuthModel()
    {
        $memberClass = $this->getConfigure('member', '');
        if (!$memberClass || !class_exists($memberClass)) {
            throw new AppException("没有绑定会员关联模型或绑定模型不存在[$memberClass]");
        }
        
        $parentClass = OAuthModel::class;
        if (!is_subclass_of($memberClass, $parentClass)) {
            throw new AppException("绑定会员关联模型类必须集成[{$parentClass}]接口");
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
     * @return int 记录ID
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
        $insert->userInfo  = $oauthInfo->getUserInfo();
        
        if (false === $insertId = $this->insertData($insert)) {
            throw new VerifyException('该账户已被他人绑定', 'repeat');
        }
        
        return $insertId;
    }
    
    
    /**
     * 通过OAuth数据进行绑定
     * @param OAuth $oauth
     * @return int|false 返回false代表没有绑定，返回int代表已绑定的记录ID
     * @throws ParamInvalidException
     * @throws SQLException
     * @throws VerifyException
     */
    protected function bindByOAuth(OAuth $oauth)
    {
        $oauthInfo = $oauth->onGetInfo();
        if ($info = $this->checkBindByOpenid($oauthInfo->getOpenId(), $oauthInfo->getType())) {
            return $info['id'];
        }
        
        
        // 是否在其他的客户端绑定过
        if ($oauthInfo->getUnionId() && false !== $unionInfo = $this->checkBindByUnionId($oauthInfo->getUnionId(), $oauthInfo->getUnionType())) {
            return $this->bindByOAuthAndUserId($oauth, $unionInfo['user_id']);
        }
        
        return false;
    }
    
    
    /**
     * 通过OAuth数据和注册数据进行绑定
     * @param OAuth    $oauth
     * @param Field    $register 用户注册的数据，未绑定未注册的时候使用
     * @param Field    $update 用户更新数据，已注册已绑定的时候使用
     * @param callable $repeat 查重回调，return int 代表要更新的用户信息，否则执行注册
     * @return array [会员ID, 绑定记录ID]
     * @throws ParamInvalidException
     * @throws SQLException
     * @throws VerifyException
     * @throws Exception
     */
    public function bindByOAuthOrRegister(OAuth $oauth, Field $register, Field $update, callable $repeat)
    {
        // 执行绑定
        // 1. 用户已经在同厂商不通客户端登录，如：已经在公众号绑定，没有在app上绑定
        // 2. 用户从未登录过
        $oauthId = $this->bindByOAuth($oauth);
        
        
        $this->startTrans();
        try {
            $memberModel = $this->getOAuthModel();
            
            
            // 没有绑定，两种情况
            // 1. 用户未注册过，则肯定没有绑定
            // 2. 用户已注册过，但是没有绑定任何记录
            if (false === $oauthId) {
                // 执行查重回调
                $userId = call_user_func($repeat);
                
                // 回调返回数组，代表用户已注册，则直接为其绑定
                if ($userId > 0) {
                    $oauthId = $this->bindByOAuthAndUserId($oauth, $userId);
                }
                
                //
                // 否则进行注册并绑定
                else {
                    $userId = $memberModel->onOAuthRegister($register);
                    if ($userId < 1) {
                        throw new AppException('onOAuthRegister方法必须返回有效的会员ID');
                    }
                    
                    $oauthId = $this->bindByOAuthAndUserId($oauth, $userId);
                }
            } else {
                $oauthInfo = $this->lock(true)->getInfo($oauthId);
                $userId    = $oauthInfo['user_id'];
                
                // 有数据则更新
                if ($update->getDBData()) {
                    $memberModel->onOAuthUpdate($userId, $update);
                }
            }
            
            
            $this->commit();
            
            return [$userId, $oauthId];
        } catch (Exception $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 执行登录
     * @param OAuth $oauth
     * @return array|false 返回false代表用户没有绑定该登录方式，返回array中包含2个数组: [用户信息, 绑定记录信息]
     * @throws Exception
     */
    public function login(OAuth $oauth)
    {
        $oauthInfo = $oauth->onGetInfo();
        if (!$oauthInfo->getOpenId() && !$oauthInfo->getUnionId()) {
            throw new ParamInvalidException('openid,unionId');
        }
        if ($oauthInfo->getType() < 1) {
            throw new ParamInvalidException('type');
        }
        
        $memberModal = $this->getOAuthModel();
        
        $this->startTrans();
        try {
            $info = $this->lock(true)->checkBindByOpenid($oauthInfo->getOpenId(), $oauthInfo->getType());
            
            // 绑定记录不存在则需要绑定
            if (!$info) {
                $return = false;
                goto commit;
            }
            
            // 执行会员登录
            if (!$userInfo = $memberModal->onOAuthLogin($info['user_id'], $oauthInfo, $info)) {
                $return = false;
                goto commit;
            }
            
            // 更新绑定记录登录信息
            $save             = MemberOauthField::init();
            $save->nickname   = $oauthInfo->getNickname();
            $save->avatar     = $oauthInfo->getAvatar();
            $save->sex        = $oauthInfo->setSex();
            $save->userInfo   = $oauthInfo->getUserInfo();
            $save->loginTotal = ['exp', 'login_total+1'];
            $save->loginIp    = $this->app->request->ip();
            $save->loginTime  = time();
            $save->lastIp     = ['exp', 'login_ip'];
            $save->lastTime   = ['exp', 'login_time'];
            if (false === $this->where('id', '=', $info['id'])->saveData($save)) {
                throw new SQLException('更新绑定记录登录信息失败', $this);
            }
            $return = [$userInfo, $info];
            
            commit:
            $this->commit();
            
            return $return;
        } catch (Exception $e) {
            $this->rollback();
            
            throw $e;
        }
    }
    
    
    /**
     * 通过Openid获取绑定记录
     * @param string $openid
     * @param int    $type 登录类型
     * @return array
     * @throws SQLException
     */
    public function getInfoByOpenId($openid, $type)
    {
        $where         = MemberOauthField::init();
        $where->openid = trim($openid);
        $where->type   = intval($type);
        $info          = $this->whereof($where)->findData();
        if (!$info) {
            throw new SQLException('绑定记录不存在', $this);
        }
        
        return static::parseInfo($info);
    }
    
    
    /**
     * 通过OpenId获取缓存数据
     * @param $openid
     * @param $type
     * @return array
     * @throws SQLException
     */
    public function getInfoByOpenIdFromCache($openid, int $type)
    {
        $openid = trim($openid);
        $key    = md5($openid . '.' . $type);
        $info   = $this->getCache($key);
        if (!$info) {
            $info = $this->getInfoByOpenId($openid, $type);
            $this->setCache($key, $info);
        }
        
        return $info;
    }
    
    
    /**
     * 通过openid校验是否绑定
     * @param string $openid
     * @param int    $type 登录类型
     * @return array|false
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
     * @return array
     * @throws SQLException
     */
    public function getInfoByUnionId($unionId, $type)
    {
        $where            = MemberOauthField::init();
        $where->unionid   = trim($unionId);
        $where->unionType = intval($type);
        $info             = $this->whereof($where)->findData();
        if (!$info) {
            throw new SQLException('绑定记录不存在', $this);
        }
        
        return static::parseInfo($info);
    }
    
    
    /**
     * 通过unionId校验是否绑定
     * @param string $unionId
     * @param int    $type 登录类型
     * @return array|false
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
     * @return array
     * @throws SQLException
     */
    public function getInfoByUserId($userId, int $type, int $unionType)
    {
        $where            = MemberOauthField::init();
        $where->userId    = $userId;
        $where->type      = $type;
        $where->unionType = $unionType;
        if (!$info = $this->whereof($where)->findData()) {
            throw new SQLException('Oauth记录不存在', $this);
        }
        
        return self::parseInfo($info);
    }
    
    
    /**
     * 通过会员ID检测是否绑定
     * @param int $userId
     * @param int $type
     * @param int $unionType
     * @return array|bool
     */
    public function checkBindByUserId($userId, int $type, int $unionType)
    {
        try {
            return $this->getInfoByUserId($userId, $type, $unionType);
        } catch (SQLException $e) {
            return false;
        }
    }
    
    
    public static function parseList($list)
    {
        return parent::parseList($list, function($list) {
            foreach ($list as $i => $r) {
                $r['user_info'] = unserialize($r['user_info']);
                $list[$i]       = $r;
            }
            
            return $list;
        });
    }
}