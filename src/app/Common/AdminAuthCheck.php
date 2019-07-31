<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/17
 *+----------------------------------------------------------------------
 *| Time: 17:40
 *+----------------------------------------------------------------------
 */

namespace App\Common;

use App\Model\AdminAuth;
use App\Model\AdminAuthGroupAccess;
use App\Model\AdminAuthRule;
use PhalApi\Exception\BadRequestException;


/**
 * 检查用户是否具备此操作权限
 * Class AdminAuthCheck
 * @package App\Common
 */
class AdminAuthCheck
{
    /**
     * 用户权限检测

     */
    public function handle() {

        $ApiAuth = $_SERVER['HTTP_APIAUTH'];
        $userInfo =  \PhalApi\DI()->cache->get('Login:' . $ApiAuth);
        if(!$userInfo){
            throw new BadRequestException('账户过期，请重新登录',1);
        }
        $userInfo = json_decode($userInfo,true);
        $url=\PhalApi\DI()->request->getService();
        $url=str_replace('.','/',$url);
        $url=str_replace('App','admin',$url);
        if (!$this->checkAuth($userInfo['id'],  $url)) {
           throw new BadRequestException('非常抱歉，您没有权限这么做！',1);
        }
        return true;
    }

    /**
     * 检测用户权限

     */
    private function checkAuth($uid, $route) {
        $isSupper = \App\isAdministrator($uid);
        if (!$isSupper) {
            $rules = $this->getAuth($uid);
            return in_array($route, $rules);
        } else {
            return true;
        }

    }

    /**
     * 根据用户ID获取全部权限节点
     */
    private function getAuth($uid) {
        $authGroupAccess=new AdminAuthGroupAccess();
        $groups = $authGroupAccess->getInfoFromUid($uid);
        if (isset($groups) && $groups['group_id']) {
            $authGroup=new AdminAuth();
            $openGroup=$authGroup->getInfo($groups['group_id']);
            if (isset($openGroup)) {
                $openGroupArr = [];
                foreach ($openGroup as $group) {
                    $openGroupArr[] = $group['id'];
                }
                $Rule=new AdminAuthRule();
                $allRules=$Rule->getInfoInGid($openGroupArr);
                if (isset($allRules)) {
                    $rules = [];
                    foreach ($allRules as $rule) {
                        $rules[] = $rule['url'];
                    }
                    $rules = array_unique($rules);

                    return $rules;
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
