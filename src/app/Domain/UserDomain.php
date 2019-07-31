<?php


namespace App\Domain;


use App\Model\AdminAuthGroupAccess;
use App\Model\AdminUser;
use app\model\ApiAuthGroupAccess;

class UserDomain
{
    private  $model;

    public function __construct(){
        $this->model=new AdminUser();
    }
    /**
     * 将二维数组变成指定key
     * @param $array
     * @param $keyName
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public static function buildArrByNewKey($array, $keyName = 'id') {
        $list = array();
        foreach ($array as $item) {
            $list[$item[$keyName]] = $item;
        }
        return $list;
    }
    public function index($where,$limit){
        $listInfo=$this->model->index($where,$limit);
        $Access=new AdminAuthGroupAccess();
        $userGroup =$Access->getAllInfo();
        $userGroup = self::buildArrByNewKey($userGroup, 'uid');
        foreach ($listInfo['data'] as $k=>$v){
            $listInfo['data'][$k]['userData']['create_time']=date('Y-m-d H:i:s',$v['create_time']);
            $listInfo['data'][$k]['userData']['login_times']=$v['login_times'];
            $listInfo['data'][$k]['userData']['update_time']=date('Y-m-d H:i:s',$v['update_time']);
            $listInfo['data'][$k]['userData']['last_login_ip']=$v['last_login_ip'];
            $listInfo['data'][$k]['userData']['last_login_time']=$v['last_login_time']?date('Y-m-d H:i:s',$v['last_login_time']):0;
            if (isset($userGroup[$v['id']])) {
                $listInfo['data'][$k]['group_id'] = explode(',', $userGroup[$v['id']]['group_id']);
            } else {
                $listInfo['data'][$k]['group_id'] = [];
            }
        }
        return $listInfo;
    }
    public function addDomain($params){
        return $this->model->add($params);
    }
    public function edit($id,$status){
        return $this->model->edit($id,$status);
    }
    public function del($id){
        return $this->model->del($id);
    }
    public function getUserInGroup($page,$size,$gid){
        $authGroupAccess=new AdminAuthGroupAccess();
        $userData=$authGroupAccess->getInfoFromGroupIdMisty($gid);
        $uidArr = array_column($userData, 'uid');
        $userInfo =$this->model->getAllUser($uidArr,$page,$size);
        foreach ($userInfo['data'] as $k=> $v){
                $userInfo['data'][$k]['last_login_ip'] = $v['last_login_ip'];
                $userInfo['data'][$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
                $userInfo['data'][$k]['login_times'] = $v['login_times'];
                $userInfo['data'][$k]['update_time'] = date('Y-m-d H:i:s', $v['update_time']);
                $userInfo['data'][$k]['last_login_time'] = $v['last_login_time'] ? date('Y-m-d H:i:s', $v['last_login_time']) : 0;
        }
        return $userInfo;
    }
}
