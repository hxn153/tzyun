<?php


namespace Website\Domain;


class Blog
{
    public function getBlog($push){
        $model=new \Website\Model\Blog();
        return $model->getBlog($push);
    }
    public function getBlogFormTag($tag){
        $model=new \Website\Model\Blog();
        $ret= $model->getBlogFormTag('%'.$tag.'%');
        foreach ($ret as& $v){
            $v['tag']=unserialize($v['tag']);
        }
        return $ret;
    }
    public function getBlogInfo($id){
        $model=new \Website\Model\Blog();
        $ret= $model->getBlogInfo($id);
        $ret['tag']=unserialize($ret['tag']);
        return $ret;
    }
}
