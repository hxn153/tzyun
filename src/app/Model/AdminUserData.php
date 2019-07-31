<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class AdminUserData extends NotORM {

  protected function getTableName($id) {
    return 'admin_user_data';
  }
  
  public function getLoginUserData($uid){
    return $this->getORM()->where('uid', $uid)->fetchOne();
  }

  public function updateLoginUserData($uid, $data){
    return $this->getORM()->where('uid', $uid)->update($data);
  }
    
}
