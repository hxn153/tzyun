<?php


namespace Website\Model;


use PhalApi\Model\NotORMModel;

class Blog extends NotORMModel
{
    public function getBlog($push){
        if($push){
            return $this->getORM()->where('push=?',$push)->fetchAll();
        }else{
            return $this->getORM()->fetchAll();
        }
    }
    public function getBlogFormTag($tag){
        return $this->getORM()->where('tag LIKE ?',$tag)->fetchAll();
    }
    public function getBlogInfo($id){
        return $this->getORM()->where('id=?',$id)->fetchOne();
    }
}
