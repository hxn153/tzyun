<?php


namespace App\Api;


use App\Domain\AuthDomain;
use App\Domain\MenuDomain;
use App\Model\AdminAuthGroupAccess;
use App\Model\AdminAuthRule;
use app\model\ApiAuthGroup;
use app\model\ApiAuthGroupAccess;
use app\model\ApiAuthRule;
use app\model\ApiMenu;
use app\model\ApiUser;
use app\model\ApiUserData;
use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 权限管理
 * Class Auth
 * @package App\Api
 */
class Auth extends Api
{
    public function getRules()
    {
        return array(
            'index'=>array(
                'size'=>array('name'=>'size','type'=>'int','source' => 'get','require'=>true,'desc'=>'分页：第几条'),
                'page'=>array('name'=>'page','type'=>'int','source' => 'get','require'=>true,'desc'=>'分页：取几页'),
                'keywords'=>array('name'=>'keywords','source' => 'get','desc'=>'昵称或真实姓名的关键字','default'=>null),
                'status'=>array('name'=>'status','type'=>'string','source' => 'get','desc'=>'根据状态查询','default'=>'null'),
            ),
            'getRuleList'=>array(
                'group_id'=>array('name'=>'group_id','type'=>'int','source' => 'get','require'=>false,
                                 'desc'=>'编辑时请传值权限组ID获取相关授权','default'=>'null'),
            ),
            'add'=>array(
                'name'=>array('name'=>'name','type'=>'string','source' => 'post','require'=>true,'desc'=>'组名称'),
                'description'=>array('name'=>'description','source' => 'post','type'=>'string','require'=>false,'desc'=>'组描述'),
                'rules'=>array('name'=>'rules','type'=>'array','source' => 'post','require'=>true,'desc'=>'组授权,传入授权菜单url'),

            ),
            'edit'=>array(
                'id'=>array('name'=>'id','type'=>'int','source' => 'post','require'=>true,'desc'=>'权限组ID'),
                'name'=>array('name'=>'name','type'=>'string','source' => 'post','require'=>true,'desc'=>'组名称'),
                'description'=>array('name'=>'description','type'=>'string','source' => 'post','require'=>false,'desc'=>'组描述'),
                'rules'=>array('name'=>'rules','type'=>'array','source' => 'post','require'=>true,'desc'=>'组授权,传入授权菜单url'),

            ),
            'del'=>array(
                'id'=>array('name'=>'id','type'=>'int','source' => 'get','require'=>true,'desc'=>'权限组ID'),
            ),
            'changeStatus'=>array(
                'id'=>array('name'=>'id','type'=>'int','source' => 'get','require'=>true,'desc'=>'权限组ID'),
                'status'=>array('name'=>'status','type'=>'int','source' => 'get','require'=>true,'desc'=>'组状态：为1正常，为0禁用'),
            ),
          'delMember'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','source' => 'get','require'=>true,'desc'=>'权限组的用户UID'),
                'gid'=>array('name'=>'gid','type'=>'int','source' => 'get','require'=>true,'desc'=>'权限组ID'),
            ),
        );
    }
    /**
     * 获取权限组列表
     * @desc get请求 返回符合条件的所有用户
     * @return array list 所有权限组数据
     * @return int count 共多少条数据
     * @Exception 401 参数请求错误或未传值
     */
    public function index() {
        $size= $this->size;
        $page = $this->page;
        $keywords = $this->keywords;
        $status = $this->status;
        if(!$size || !$page){
            throw new BadRequestException('参数错误',1);
        }
        $where=[];
        if($keywords){
            $where['name'] = " like '%{$keywords}%'";
        }
        if ($status === '1' || $status === '0') {
            $where['status'] = " = ". $status;
        }
        $limit['size']=$size;
        $limit['page']=($page-1)*$size;
        $model= new AuthDomain();
        $listInfo = $model->index($limit,$where);
        return array(
            'list'  => $listInfo['data'],
            'count' =>$listInfo['count']
        );
    }

    /**
     * 获取组所在权限列表
     * @desc get请求，获取组授权(菜单)数据库
     * @return array list 所有权限(菜单)数据
     * @return array children  为二级菜单
     * @return boolean checked 如传入groupId已有权限菜单将为true
     */
    public function getRuleList() {
        $groupId = $this->group_id;
        $menuInfo=new MenuDomain();
        $list =$menuInfo->index();
        $list = $menuInfo->listToTree($list);

        $rules = [];
        if ($groupId) {
        $authRule=new AdminAuthRule();
            $rules =$authRule->getInfoFromGroupId($groupId);
            $rules = array_column($rules, 'url');
        }
        $newList = $this->buildList($list, $rules);

        return array('list' => $newList);
    }



    /**
     * 新增组
     * @desc POST请求 新增权限组
     * @return string ret 成功或失败
     * @Exception 401 参数结构错误
     * @Exception 402 添加失败
     */
    public function add() {
        if(!is_array($this->rules)){
            throw new BadRequestException('组授权请传入数组',1);
        }
        $rules = explode(',',implode(',',$this->rules));
        $postData['description']=$this->description;
        $postData['name']=$this->name;

        $model=new AuthDomain();
        $res =$model->add($postData);
        if ($res === false) {
            throw new BadRequestException('添加失败',2);
        } else {
            if ($rules) {
                $insertData = [];
                foreach ($rules as $k=> $value) {
                    if ($value) {
                        $insertData[$k]['group_id'] =$res;
                        $insertData[$k]['url'] =$value;
                        $insertData[$k]['status'] =1;
                    }
                }
                $authRule=new AdminAuthRule();
                $authRule->add($insertData);
            }

            return '添加成功';
        }
    }
    /**
     * 获取全部已开放的可选组
     * @desc 用于编辑
     */
    public function getGroups() {
        $authGroup=new AuthDomain();
        $listInfo =$authGroup->getInfoFromGroups();
        $count = count($listInfo);
        return array('list'  => $listInfo,'count' => $count);
    }
    /**
     * 编辑用户
     * @desc POST请求
     * @return string data 操作成功
     * @Exception 401 参数错误
     * @Exception 402 操作失败
     */
    public function edit() {
        $rules = explode(',',implode(',',$this->rules));
        if(!$this->id){
            throw new BadRequestException('参数错误',1);
        }
        $postData['description']=$this->description;
        $postData['name']=$this->name;
        $postData['rules']=$rules;
        $postData['id']=$this->id;
        if ($rules) {
            $this->editRule($postData);
        }
        unset($postData['rules']);
        $authGroup=new AuthDomain();
        $res = $authGroup->edit($postData['id'],$postData);
        if ($res === false) {
            throw new BadRequestException('操作失败',2);
        } else {
            return '操作成功';
        }
    }
    /**
     * 删除组
     * @desc  get请求 删除权限组，同时所在组的成员将失去此组的权限
     * @Exception 401 缺少必要参数
     */
    public function del() {
        $id = $this->id;
        if (!$id) {
            throw new BadRequestException('缺少必要参数',1);
        }
        try{
        $authGroupAccess=new AdminAuthGroupAccess();
        $listInfo =$authGroupAccess->getInfoFromGroupIdMisty($id);

        if ($listInfo) {
            foreach ($listInfo as $value) {
                $oldGroupArr = explode(',', $value['group_id']);
                $key = array_search($id, $oldGroupArr);
                unset($oldGroupArr[$key]);
                $newData = implode(',', $oldGroupArr);
                $newValue['group_id'] = $newData;
                $authGroupAccess->edit($value['uid'],$newValue);
            }
        }
        $authGroup=new AuthDomain();
        $authRule=new AdminAuthRule();
        $authGroup->del($id);
        $authRule->delFromGroupId($id);
        return '操作成功';
        } catch (\PhalApi\Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 权限组状态编辑
     * @desc get请求 权限组状态编辑
     * @return string data 成功
     * @Exception 401 非法操作,请传入正确参数
     * @Exception 402 操作失败
     */
    public function changeStatus() {
        $id = $this->id;
        $status = $this->status;
        if(!$id || $status !==0 && $status !==1){
            throw new BadRequestException('请传入正确参数',1);
        }
        $model=new AuthDomain();
        $res =$model->changeStatus($id,$status);
        if ($res === false) {
            throw new BadRequestException('操作失败',1);
        } else {
            return '操作成功';
        }
    }

    /**
     * 从指定组中删除指定用户
     * @desc  get请求 从指定组中删除指定用户
     * @return string data 操作成功
     * @Exception 401 缺少必要参数
     * @Exception 402 操作失败
     */
    public function delMember() {
        $gid = $this->gid;
        $uid = $this->uid;
        if (!$gid || !$uid) {
           throw new BadRequestException( '缺少必要参数',1);
        }
        $authGroupAccess=new AdminAuthGroupAccess();
        $oldInfo =$authGroupAccess->getInfoFromUid($uid);
        $oldGroupArr = explode(',', $oldInfo['group_id']);
        $key = array_search($gid, $oldGroupArr);
        unset($oldGroupArr[$key]);
        $newData = implode(',', $oldGroupArr);
        $res = $authGroupAccess->edit($uid,['group_id'=>$newData]);
        if ($res === false) {
           throw new BadRequestException('操作失败',2);
        } else {
            return '操作成功';
        }
    }

    /**
     * 构建适用前端的权限数据
     * @param $list
     * @param $rules
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function buildList($list, $rules) {
        $newList = [];
        foreach ($list as $key => $value) {
            $newList[$key]['title'] = $value['name'];
            $newList[$key]['key'] = $value['url'];
            if (isset($value['_child'])) {
                $newList[$key]['expand'] = true;
                $newList[$key]['children'] = $this->buildList($value['_child'], $rules);
            } else {
                if (in_array($value['url'], $rules)) {
                    $newList[$key]['checked'] = true;
                }
            }
        }

        return $newList;
    }
    /**
     * 编辑权限细节
     * @throws \Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function editRule($postData) {
        $needAdd = [];
        $authRule=new AdminAuthRule();
        $has =$authRule->getInfoFromGroupId($postData['id']);
        $hasRule = array_column($has, 'url');
        $needDel = array_flip($hasRule);
        foreach ($postData['rules'] as $key => $value) {
            if (!empty($value)) {
                if (!in_array($value, $hasRule)) {
                    \PhalApi\DI()->logger->info('K：',$key);
                    $needAdd[$key]['url'] = $value;
                    $needAdd[$key]['group_id'] = $postData['id'];
                   // $needAdd[] = $data;
                } else {
                    unset($needDel[$value]);
                }
            }
        }

        if (count($needAdd)) {
            $authRule->add($needAdd);
            //(new ApiAuthRule())->saveAll($needAdd);
        }
        if (count($needDel)) {
            $urlArr = array_keys($needDel);
           // \PhalApi\DI()->logger->info('数据',$urlArr);
            $authRule->delFromGroupIdAndUrl($postData['id'],$urlArr);

        }
    }
}
