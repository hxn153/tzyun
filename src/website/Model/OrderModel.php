<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/29
 *+----------------------------------------------------------------------
 *| Time: 11:29
 *+----------------------------------------------------------------------
 */


namespace Website\Model;


use PhalApi\Model\NotORMModel;

class OrderModel extends  NotORMModel
{
    protected function getTableName($id)
    {
        return 'tzy_order';
    }

    public function orderByUid($limit,$uid,$keyword){
        $size=$limit['size'];
        $page=$size*($limit['page']-1);
            if($keyword){
                $ret=$this->getORM()->where('orderId=?',$keyword)->where('orderState=0')->where('uid',$uid)->limit($page,$size)->fetchAll();
                $count=$this->getORM()->where('orderId=?',$keyword)->where('orderState=0')->where('uid',$uid)->count();
            }else{
                $ret=$this->getORM()->where('uid',$uid)->where('orderState=0')->limit($page,$size)->fetchAll();
                $count=$this->getORM()->where('uid',$uid)->where('orderState=0')->count();
            }
        $page_count=ceil($count/$size);
        return ['data'=>$ret,'page_info'=>['count'=>$count,'page_count'=>$page_count]];
    }
    public function orderByUidPay($limit,$uid,$keyword){
        $size=$limit['size'];
        $page=$size*($limit['page']-1);
        if($keyword){
            $ret=$this->getORM()->where('orderId=?',$keyword)->where('orderState=1')->where('uid',$uid)->limit($page,$size)->fetchAll();
            $count=$this->getORM()->where('orderId=?',$keyword)->where('orderState=1')->where('uid',$uid)->count();
        }else{
            $ret=$this->getORM()->where('uid',$uid)->where('orderState=1')->limit($page,$size)->fetchAll();
            $count=$this->getORM()->where('uid',$uid)->where('orderState=1')->count();
        }
        $page_count=ceil($count/$size);
        return ['data'=>$ret,'page_info'=>['count'=>$count,'page_count'=>$page_count]];
    }
}
