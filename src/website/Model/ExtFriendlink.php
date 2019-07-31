<?php


namespace Website\Model;


use PhalApi\Model\NotORMModel;

class ExtFriendlink extends NotORMModel
{
    protected function getTableName($id) {
        return 'ext_friendlink';  // 手动设置表名为 my_user
    }
    public function getFriendlinkInfo(){
        return $this->getORM()->fetchAll();
    }
}
