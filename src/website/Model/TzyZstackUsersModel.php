<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/25
 *+----------------------------------------------------------------------
 *| Time: 17:55
 *+----------------------------------------------------------------------
 */


namespace Website\Model;


use PhalApi\Model\NotORMModel;

class TzyZstackUsersModel extends NotORMModel
{
    public function getTableName($id)
    {
        return 'tzy_zstack_users';
    }
    public function getInfoByWhere($where){
        return $this->getORM()->where($where)->fetchAll();
    }
    /**
     * 检测账号是否绑定Ztack
     */
    public function checkGrade($uid){
        $ret= $this->getORM()->where('uid',$uid)->fetchOne();
        return $ret;
    }
    public function getSessionUuid($uid){
        //return $this->getORM()->where('uid',$where)->fetchAll();
        $sql="SELECT b.accountName,b.`password` FROM `tzy_zstack_users` a LEFT JOIN `zta_account` b ON a.zstack_uuid=b.`localuuid` WHERE a.uid=?";
        $userinfo=$this->getORM()->queryAll($sql,array($uid));
        return $userinfo;
    }
}
