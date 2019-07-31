<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/23
 *+----------------------------------------------------------------------
 *| Time: 11:24
 *+----------------------------------------------------------------------
 */


namespace Website\Domain;


use Website\Model\OrderModel;

class OrderDomain
{
    private $model;
    public function __construct()
    {
        $this->model=new OrderModel();
    }

    public function orderByUid($limit,$uid,$keyword,$isPay){
        if($isPay){
            return $this->model->orderByUidPay($limit,$uid,$keyword);
        }else{
            return $this->model->orderByUid($limit,$uid,$keyword);
        }

    }

}
