<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class AdminUser extends NotORM {

  protected function getTableName($id) {
    return 'admin_user';
  }

  public function getLoginUser($username, $password){
    return $this->getORM()->and('username',$username)->and('password', $password)->fetchOne();
    // $sql="SELECT * FROM admin_user WHERE username = ? AND password = ?";
  }

  public function index($where,$limit){
      $whereAnd="";
        $num=12;
      foreach ($where as $k=>$v){
          if($num===12){
              $whereAnd.="WHERE ".$k." ".$v;
          }else{
              $whereAnd.=" AND ".$k." ".$v;
          }
          $num++;
      }

      $sql="SELECT a.*,b.login_times,b.last_login_ip,b.last_login_time,b.uid,b.head_img
                FROM admin_user a LEFT JOIN admin_user_data b ON a.id=b.uid 
              ".$whereAnd. " ORDER BY b.last_login_time DESC limit ".$limit['page'].",".$limit['size'];
      $countSql="SELECT a.*,b.login_times,b.last_login_ip,b.last_login_time,b.uid,b.head_img
                FROM admin_user a LEFT JOIN admin_user_data b ON a.id=b.uid 
              ".$whereAnd. " ORDER BY b.last_login_time DESC";
      $ret= $this->getORM()->queryAll($sql,array());
      $count= $this->getORM()->queryAll($countSql,array());
    return array('data'=>$ret,'count'=>count($count));
    // return $this->getORM()->queryAll($sql,array($username,$password));
  }
  public function add($params){
      $orm=$this->getORM();
      $orm->insert($params);
      return $orm->insert_id();
  }
  public function edit($id,$status){
      return $this->getORM()->where('id=?',$id)->update($status);
  }
    public function del($id){
        return $this->getORM()->where('id=?',$id)->delete();
    }
    public function getAllUser($uidArr,$page,$size){
        $uidString=implode(',',$uidArr);
        if(!$uidString){
          return array();
        }
        $sql="SELECT a.*,b.login_times,b.last_login_ip,b.last_login_time,b.uid,b.head_img
                FROM admin_user a LEFT JOIN admin_user_data b ON a.id=b.uid  where a.id in (".$uidString.")
               ORDER BY b.last_login_time DESC limit ".$page.",".$size;
        $countSql="SELECT a.*,b.login_times,b.last_login_ip,b.last_login_time,b.uid,b.head_img
                FROM admin_user a LEFT JOIN admin_user_data b ON a.id=b.uid  where a.id in (".$uidString.")
               ORDER BY b.last_login_time DESC";
        $data=$this->getORM()->queryAll($sql,array());
        $count=$this->getORM()->queryAll($countSql,array());

        return ['data'=>$data,'count'=>count($count)];
    }
}
