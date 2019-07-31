<?php


namespace Zstack\Domain\v1;

use Website\Model\USER as ModelArticle;


class Accounts
{
    private $model;
    public function __construct()
    {
        $this->model=new \Zstack\Model\v1\Accounts();
    }
    public function createAcc($params){
        $data['name']=$params['name'];
        $data['password']=hash('sha512',$params['password']);
        $create['params']=$data;
        return $this->model->createAccount($create);
    }
    public function queryAcc($params){
        return $this->model->queryAccount($params['uuid']);
    }
    public function delAcc($params){
        return $this->model->deleteAccount($params['uuid']);
    }
    public function eidtAcc($params){
        $data['name']=$params['name'];
        $data['password']=hash('sha512',$params['password']);
        $create['updateAccount']=$data;
        $create['uuid']=$params['uuid'];
        return $this->model->updateAccount($create);
    }



}
