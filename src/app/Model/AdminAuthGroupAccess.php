<?php


namespace App\Model;


use PhalApi\Model\NotORMModel;

/**
 * 权限与用户对应表
 * Class AdminAuthGroupAccess
 * @package App\Model
 */
class AdminAuthGroupAccess extends NotORMModel
{
    public function getTableName($id)
    {
        return 'admin_auth_group_access';
    }

    /**
     * 获取用户和组所对应的全部数据
     * @param $size 分页开始
     * @param $page 分页数量
     * @param $where条件
     */
    public function getAuthWithUser($page,$size,$where){
        $sql="SELECT a.group_id,b.* FROM admin_auth_group_access a LEFT JOIN admin_user b ON a.uid=b.id 
                WHERE a.group_id ".$where."ORDER BY b.create_time DESC limit ".$page.','.$size;
        $ret= $this->getORM()->queryAll($sql,array());
        return $ret;
    }
    public function getAllInfo(){
        return $this->getORM()->fetchAll();
    }

    //根据GroupId模糊搜索所有信息
    public function getInfoFromGroupIdMisty($groupId){
        return $this->getORM()->where('group_id like?',"%{$groupId}%")->fetchAll();
    }
    public function getInfoFromUid($uid){
        return $this->getORM()->where('uid=?',$uid)->fetchOne();
    }
    public function edit($uid, $data)
    {
        return $this->getORM()->where('uid=?',$uid)->update($data);
    }
    public function del($uid)
    {
        return $this->getORM()->where('uid=?',$uid)->delete();
    }

}
