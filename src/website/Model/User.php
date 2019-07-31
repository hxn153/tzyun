<?php


namespace Website\Model;


use PhalApi\Exception\BadRequestException;
use PhalApi\Model\NotORMModel;

class User extends NotORMModel
{
    public function getTableName($id)
    {
        return 'tzy_users';
    }
    public function regModel($data){
        $db=$this->getORM();
        $ret=$db->or('mobile=?',$data['mobile'])->or('username=?',$data['mobile'])->fetchOne();
        if(!empty($ret)){
            return array('code'=>406,'msg'=>'手机号已存在');
        }
        $r= $db->insert($data);
        if($r){
            return array('code'=>1,'msg'=>'注册成功');
        }{
            return array('code'=>-1,'msg'=>'注册失败');
        }

    }
    /**
     * 个性账号登录
     * @param $username
     * @param $password
     * @return \NotORM_Result
     */
    public function getUserStyle($username,$password){
        return $this->getORM()
            ->where('username=?',$username)
            ->where('passwd=?',$password)
            ->fetchOne();
    }
    /**
     * 邮箱登录
     */
    public function getUserEmail($username,$password){
        return $this->getORM()
            ->and('email=?',$username)
            ->and('passwd=?',$password)
            ->fetchOne();
    }

    /**
     * 手机号登录
     */
    public function getUserPhone($username,$password){
        return $this->getORM()
            ->and('mobile=?',$username)
            ->and('passwd=?',$password)
            ->fetchOne();
    }
    /**
     * 登录更新数据
     */
    public function setUserLogin($where,$data){
        return $this->getORM()
            ->where($where)
            ->update($data);
    }

}
