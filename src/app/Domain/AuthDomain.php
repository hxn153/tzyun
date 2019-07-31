<?php


namespace App\Domain;


use App\Model\AdminAuth;

class AuthDomain
{
    private  $model;

    public function __construct(){
        $this->model=new AdminAuth();
    }
    public function index($limit,$where){
        return $this->model->index($limit,$where);
    }
    public function add($params){
        return $this->model->add($params);
    }
    public function changeStatus($id,$status){
        return $this->model->changeStatus($id,$status);
    }
    public function del($id){
        return $this->model->del($id);
    }
    public function edit($id,$data){
        return $this->model->edit($id,$data);
    }
    public function getInfoFromGroups(){
        return $this->model->getInfoFromGroups();
    }
}
