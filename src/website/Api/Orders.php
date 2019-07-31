<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/22
 *+----------------------------------------------------------------------
 *| Time: 15:10
 *+----------------------------------------------------------------------
 */


namespace Website\Api;


use PhalApi\Api;
use PhalApi\Exception\BadRequestException;
use Website\Domain\OrderDomain;

/**
 * 订单管理
 * Class Orders
 * @package App\Api
 */
class Orders extends Api
{
    public function getRules()
    {
        return array(
          'index'=>array(
              'page'=>array('name'=>'page_num','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：第几页'),
              'size'=>array('name'=>'page_data_count','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：取几条'),
              'keyword'=>array('name'=>'keyword','type'=>'string','source'=>'get','require'=>false,'desc'=>'查询订单号'),
          ),
            'payorders'=>array(
                'page'=>array('name'=>'page_num','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：第几页'),
                'size'=>array('name'=>'page_data_count','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：取几条'),
                'keyword'=>array('name'=>'keyword','type'=>'string','source'=>'get','require'=>false,'desc'=>'查询订单号'),
            )
        );
    }


    /**
     *未支付订单
     * @desc GET
     * @return array data  数据
     * @Exception 401 账户过期
     */
    public function index(){
        //$token=$_SERVER['HTTP_X_TCYUN_TOKEN'];
        $userInfo=\PhalApi\DI()->cache->get('Login:' . 'e641d243f6c5d9e86140e49b30178b1e');
        $userInfo=json_decode($userInfo,true);
        if(!$userInfo){
            throw new BadRequestException('账户过期，请重新登录',1);
        }
        $model=new OrderDomain();
        $limit['page']=$this->page;
        $limit['size']=$this->size;
        $keyword=$this->keyword;
        $ret=$model->orderByUid($limit,$userInfo['uid'],$keyword,0);
        return $ret;
    }

    /**
     *已支付订单
     * @desc GET
     * @return array data  数据
     * @Exception 401 账户过期
     */
    public function payorders(){
        //$token=$_SERVER['HTTP_X_TCYUN_TOKEN'];
        $userInfo=\PhalApi\DI()->cache->get('Login:' . 'e641d243f6c5d9e86140e49b30178b1e');
        $userInfo=json_decode($userInfo,true);
        if(!$userInfo){
            throw new BadRequestException('账户过期，请重新登录',1);
        }
        $model=new OrderDomain();
        $limit['page']=$this->page;
        $limit['size']=$this->size;
        $keyword=$this->keyword;
        $ret=$model->orderByUid($limit,$userInfo['uid'],$keyword,1);
        return $ret;

    }

    /**
     *信用订单 未完成
     * @desc
     * @return
     * @Exception
     */
    public function preorders(){

    }

}
