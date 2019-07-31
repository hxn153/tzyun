<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhoStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/11
 *+----------------------------------------------------------------------
 *| Time: 11:20
 *+----------------------------------------------------------------------
 */
namespace App\Api;


use App\Domain\UserDomain;
use App\Model\AdminAuthGroupAccess;
use App\Model\AdminUser;
use App\Model\AdminUserData;
use app\model\ApiAuthGroupAccess;
use app\model\ApiUser;
use app\model\ApiUserData;
use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 用户管理
 * Class User
 * @package App\Api
 */
class User extends Api

{
    public function getRules()
    {
        return array(
          'index'=>array(
              'page'=>array('name'=>'page','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：第几页'),
              'size'=>array('name'=>'size','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：取几条'),
              'type'=>array('name'=>'type','type'=>'int','source'=>'get','desc'=>'查询条件，1：真实姓名，2：昵称，与keywords配合使用','default'=>null),
              'keywords'=>array('name'=>'keywords','source'=>'get','desc'=>'昵称或真实姓名的关键字','default'=>null),
              'status'=>array('name'=>'status','source'=>'get','type'=>'string','desc'=>'根据状态查询','default'=>null),
          ),
            'add'=>array(
                'username'=>array('name'=>'username','type'=>'string','require'=>true,'source'=>'post','desc'=>'真实姓名'),
                'nickname'=>array('name'=>'nickname','type'=>'string','require'=>true,'source'=>'post','desc'=>'用户账号'),
                'password'=>array('name'=>'password','require'=>true,'source'=>'post','desc'=>'用户密码'),
                'group_id'=>array('name'=>'group_id','type'=>'array','require'=>true,'source'=>'post','desc'=>'权限组'),
            ),
            'changeStatus'=>array(
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'source'=>'get','desc'=>'用户主键ID'),
                'status'=>array('name'=>'status','type'=>'int','require'=>true,'source'=>'get','desc'=>'用户状态0:封号，1:正常'),
            ),
            'edit'=>array(
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'source'=>'post','desc'=>'用户主键ID'),
                'username'=>array('name'=>'username','type'=>'string','require'=>true,'source'=>'post','desc'=>'真实姓名'),
                'nickname'=>array('name'=>'nickname','type'=>'string','require'=>true,'source'=>'post','desc'=>'用户账号'),
                'password'=>array('name'=>'password','require'=>false,'source'=>'post','desc'=>'用户密码'),
                'group_id'=>array('name'=>'group_id','type'=>'array','require'=>true,'source'=>'post','desc'=>'权限组'),
            ),
            'getUsers'=>array(
                'page'=>array('name'=>'page','type'=>'int','source' => 'get','require'=>true,'desc'=>'分页：第几页'),
                'size'=>array('name'=>'size','type'=>'int','source' => 'get','require'=>true,'desc'=>'分页：取几条'),
                'gid'=>array('name'=>'gid','type'=>'int','source' => 'get','require'=>true,'desc'=>'根据权限组ID获取当前组的全部用户'),
            ),
            'own'=>array(
                'head_img'=>array('name'=>'head_img','source' => 'post','desc'=>'头像'),
                'nickname'=>array('name'=>'nickname','source' => 'post','desc'=>'昵称'),
                'oldPassword'=>array('name'=>'oldPassword','source' => 'post','desc'=>'旧密码'),
                'password'=>array('name'=>'password','source' => 'post','desc'=>'新密码'),
            ),
            'del'=>array(
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'source'=>'get','desc'=>'用户主键ID'),

            ),
        );
    }
    /**
     * 获取当前组的全部用户
     * @desc get请求 用于权限管理组成员
     * @return array list 用户所有数据
     * @return int count 共多少条
     * @Exception 401 非法操作，请传递正确参数
     */
    public function getUsers() {
        $page = $this->page;
        $size = $this->size;
        $gid = $this->gid;
        if (!$gid || !$size || !$page) {
            throw new BadRequestException('非法操作',1);
        }
        $page=$size*($page-1);
        $model=new UserDomain();
        $listInfo = $model->getUserInGroup($page,$size,$gid);
        return array('list'  => $listInfo['data'],'count' => $listInfo['count']);
    }
    /**
     * 获取用户列表
     * @desc GET请求 返回符合条件的所有用户
     * @return array list 所有用户数据
     * @return int count 共多少条数据
     * @Exception 401 参数请求错误或未传值
     */
    public function index() {
        $size = $this->size;
        $page = $this->page;
        $type = $this->type;
        $keywords = $this->keywords;
        $status = $this->status;
        if(!$size || !$page){
            throw new BadRequestException('参数错误',1);
        }

        $where = array();
        if ($status === '1' || $status === '0') {
            $where['status'] = "= ".$status;
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $where['username'] = "like '%{$keywords}%'";
                    break;
                case 2:
                    $where['nickname'] =  "like '%{$keywords}%'";
                    break;
            }
        }
        $model=new UserDomain();
        $limit['page']=($page-1)*$size;
        $limit['size']=$size;
        $listInfo = $model->index($where,$limit);

        return array('list'=>$listInfo['data'],'count'=>$listInfo['count']);
    }
    /**
     * 新增用户
     * @desc POST请求
     * @return string  ret 新增失败
     * @Exception 401 新增失败
     */
    public function add() {
        $groups = '';
        $postData['username']=$this->username;
        $postData['nickname']=$this->nickname;
        $postData['password'] = md5($this->password);
        $postData['create_ip'] = ip2long(\PhalApi\Tool::getClientIp());
        $postData['create_time'] = time();
        $postData['status'] = 1;
        if ($this->group_id) {
            $groups = trim(implode(',',$this->group_id), ',');
        }
        $model=new UserDomain();
        $res = $model->addDomain($postData);
        if ($res === false) {
            throw new BadRequestException('操作失败',1);
        } else {
            $arr['uid']= $res;
            $arr['group_id']=$groups;
            $groupAccess=new AdminAuthGroupAccess();
            $groupAccess->insert($arr);
            return '新增成功';
        }
    }
    /**
     * 用户状态编辑
     * @desc POST 请求
     * @return string msg 操作成功
     * @Exception 401 操作失败
     */
    public function changeStatus() {
        $id = $this->id;
        $status['status']= $this->status;
        $model=new UserDomain();
        $res = $model->edit($id,$status);
        if ($res === false) {
            throw new BadRequestException( '操作失败',1);
        } else {
            return '操作成功';
        }
    }
    /**
     * 编辑用户
     * @desc POST请求
     * @return string ret 操作成功
     * @Exception 401 操作失败
     */
    public function edit() {
        $groups = '';
        $postData=\PhalApi\DI()->request->getAll();
//        $postData['username']=$this->username;
//        $postData['nickname']=$this->nickname;
        $postData['update_time'] = time();
        if($postData['password']){
            $postData['password'] =md5($postData['password']);
        }else{
            unset($postData['password']);
        }
        if ($postData['group_id']) {
            $groups = trim(implode(',', $postData['group_id']), ',');
        }
        unset($postData['group_id']);
        $model=new UserDomain();

        $res = $model->edit($postData['id'],$postData);
        if ($res === false) {
           throw new BadRequestException( '操作失败',1);
        } else {
            $data['group_id']= $groups;
            $groupAccess=new AdminAuthGroupAccess();
            $groupAccess->edit($postData['id'],$data);
            return '操作成功';
        }
    }

    /**
     *修改自己的信息
     * @desc POST请求
     * @return string data 成功
     * @Exception 401 原始密码不正确
     * @Exception 402 操作失败
     */
    public function own() {
        $postData = \PhalApi\DI()->request->getAll();
        $headImg = $postData['head_img'];
        $ApiAuth = $_SERVER['HTTP_APIAUTH'];
        $userInfo =  \PhalApi\DI()->cache->get('Login:' . $ApiAuth);
        $userInfo = json_decode($userInfo,true);
        if ($postData['password'] && $postData['oldPassword']) {
            $oldPass = md5($postData['oldPassword']);
            unset($postData['oldPassword']);
            if ($oldPass === $userInfo['password']) {
                $postData['password'] = md5($postData['password']);
            } else {
               throw new BadRequestException('原始密码不正确',401);
            }
        } else {
            unset($postData['password']);
            unset($postData['oldPassword']);
        }
        //$postData['id'] = $userInfo['id'];
        unset($postData['head_img']);
        $userModel=new AdminUser();
        $res = $userModel->edit($userInfo['id'],$postData);
        if ($res === false) {
            throw new BadRequestException('操作失败',402);
        } else {
            $userDataModel=new AdminUserData();
            $userDataModel->updateLoginUserData($userInfo['id'],['head_img'=>$headImg]);

            return '操作成功';
        }
    }
    /**
     * 删除用户
     * @desc POST请求
     * @return string ret 操作成功
     * @Exception 401 缺少必要参数(主键ID)
     */
    public function del() {
        $id = $this->id;
        if (!$id) {
            throw new BadRequestException('缺少必要参数',1);
        }
        $model=new UserDomain();
        $model->del($id);
        $groupAccess=new AdminAuthGroupAccess();
        $groupAccess->del($id);
        return '操作成功';

    }

}
