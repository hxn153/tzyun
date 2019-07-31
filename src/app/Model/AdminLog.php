<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/12
 *+----------------------------------------------------------------------
 *| Time: 9:28
 *+----------------------------------------------------------------------
 */


namespace App\Model;


use PhalApi\Exception\BadRequestException;
use PhalApi\Model\NotORMModel;

class AdminLog extends NotORMModel
{
    public function getTableName($id)
    {
        return 'admin_user_action';
    }

    public function index($where,$data){
        $page=($data['page']-1)*$data['size'];
        $size=$data['size'];
        if($where){
            $ret=$this->getORM()->where($where)->limit($page,$size)->order('add_time DESC')->fetchAll();
            $count=$this->getORM()->where($where)->count();
        }else{
            $ret=$this->getORM()->limit($page,$size)->order('add_time DESC')->fetchAll();
            $count=$this->getORM()->count();
        }
        return ['data'=>$ret,'count'=>$count];
    }
    public function del($where){
        return $this->getORM()->where($where)->delete();
    }
    public function add(){
        $url=\PhalApi\DI()->request->getService();
        $ret=\PhalApi\DI()->request->getAll();
        //不是options请求和只记录app域名下的接口(后台)
        if($_SERVER['REQUEST_METHOD']!=='OPTIONS' && strstr($url,'App') && $url!=='App.Login.index' &&  $url!=='App.Login.logout'){
            //监听权限
            $listening = new \App\Common\AdminAuthCheck();
            $listening->handle();

            $ApiAuth = $_SERVER['HTTP_APIAUTH'];
        $userInfo =  \PhalApi\DI()->cache->get('Login:' . $ApiAuth);
        $userInfo = json_decode($userInfo,true);
        $url=str_replace('.','/',$url);
        $url=str_replace('App','admin',$url);
        $Menu=new AdminMenu();
        $name=$Menu->getMenuName(['url'=>$url]);
        if(!$name){
            $name['name']='获取失败';
        }
        $data=[];
        $data['action_name']=$name['name'];
        $data['uid']=$userInfo['id'];
        $data['nickname']=$userInfo['nickname'];
        $data['data']=json_encode($ret);
        $data['add_time']=time();
        $data['url']=$url;
        $this->getORM()->insert($data);
        //\PhalApi\DI()->logger->info('数据',$data);
        }
    }
}
