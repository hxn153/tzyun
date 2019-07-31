<?php


namespace App\Model;


use PhalApi\Model\NotORMModel;

/**
 * 权限细节
 * Class AdminAuthRule
 * @package App\Model
 */
class AdminAuthRule extends NotORMModel
{
    public function getTableName($id)
    {
        return 'admin_auth_rule';
    }

    /**
     * 根据groupId获取对应的权限菜单信息
     */
    public function getInfoFromGroupId($groupId){
        return $this->getORM()->where('group_id=?',$groupId)->fetchAll();
    }
    /**
     * 批量插入权限细节
     */
    public function add($params){
        $this->getORM()->insert_multi($params);
    }
    public function delFromGroupId($groupId){
        $this->getORM()->where('group_id=?',$groupId)->delete();
    }
    public function delFromGroupIdAndUrl($groupId,$url){

        $this->getORM()
            ->where('group_id=?',$groupId)
            ->where('url',$url)
            ->delete();
    }
    public function getInfoInGid($gids){

        return $this->getORM()->where('group_id',$gids)->fetchAll();
    }

}
