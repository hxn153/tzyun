<?php


namespace App\Api;


use App\Common\Msg;
use App\Domain\MenuDomain;
use app\model\ApiMenu;
use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 菜单管理
 * Class Menu
 * @package App\Api
 */
class Menu extends Api
{

    public function getRules()
    {
        return array(

            'add'=>array(
                'name'=>array('name'=>'name','require'=>true,'source'=>'post','desc'=>'菜单名'),
                'fid'=>array('name'=>'fid','require'=>true,'source'=>'post','desc'=>'父级菜单ID'),
                'url'=>array('name'=>'url','require'=>true,'source'=>'post','desc'=>'菜单url'),
                'sort'=>array('name'=>'sort','desc'=>'排序','source'=>'post','default'=>0),
            ),
            'changeStatus'=>array(
                'id'=>array('name'=>'id','require'=>true,'source'=>'get','desc'=>'ID'),
                'status'=>array('name'=>'status','require'=>true,'source'=>'get','desc'=>'状态：0显示，1：隐藏'),
            ),
            'edit'=>array(
                'id'=>array('name'=>'id','require'=>true,'source'=>'post','desc'=>'主键id'),
                'name'=>array('name'=>'name','require'=>true,'source'=>'post','desc'=>'菜单名'),
                'fid'=>array('name'=>'fid','require'=>true,'source'=>'post','desc'=>'父级菜单ID'),
                'url'=>array('name'=>'url','require'=>true,'source'=>'post','desc'=>'菜单url'),
                'sort'=>array('name'=>'sort','desc'=>'排序','source'=>'post','default'=>0,'desc'=>'状态：0显示，1：隐藏'),
            ),
            'del'=>array(
                'id'=>array('name'=>'id','require'=>true,'source'=>'get','desc'=>'主键id'),
            ),
        );
    }

    /**
     * 获取菜单列表
     * @desc GET请求 返回所有菜单 树状结构
     * @return array
     */
    public function index() {
        $model = new MenuDomain();
        $list = $model->index();
        //$list = $model->buildArrFromObj($list);
        $list = $model->formatTree($model->listToTree($list));
        return  array('list' => $list,'登录成功');

    }
    /**
     * 新增菜单
     * @desc POST 请求
     * @return string 成功或失败
     */
    public function add() {
        $postData['name']=$this->name ;
        $postData['fid']=$this->fid ;
        $postData['url']=$this->url ;
        $postData['sort']=$this->sort ;
        $postData['auth']=0 ;
        $postData['hide']=0;
        //$postData['icon']=$this->sort ;
        $postData['level']=0 ;
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $model = new MenuDomain();
        $res = $model->addMenu($postData);
        if ($res === false) {
            return  '操作失败';
        } else {
            return '操作成功';
        }
    }
    /**
     * 菜单状态编辑
     * @desc GET请求
     * @return string msg 操作成功
     * @Exception 401 操作失败
     */
    public function changeStatus() {
        $id = $this->id;
        $status['hide'] = $this->status;
        $model = new MenuDomain();
        $res =$model->updateMenu($id,$status);
        if ($res === false) {
            throw new BadRequestException('操作失败',1);
        } else {
            return '操作成功';
        }
    }
    /**
     * 编辑菜单
     * @desc POST请求
     * @return string data 操作成功或失败
     * @Exception 401 操作失败
     */
    public function edit() {
        $id = $this->id;
        $postData['name']=$this->name ;
        $postData['fid']=$this->fid ;
        $postData['url']=$this->url ;
        $postData['sort']=$this->sort ;
        if ($postData['url']) {
            $postData['url'] = 'admin/' . $postData['url'];
        }
        $model = new MenuDomain();
        $res = $model->updateMenu($id,$postData);
        if ($res === false) {
            throw new BadRequestException('操作失败',1);
        } else {
            return '操作成功';
        }
    }

    /**
     * 删除菜单
     * @desc POST请求
     * @return string data 操作成功或失败
     * @Exception 401 缺少必要参数
     * @Exception 402 存在下菜单不能被删除
     * @Exception 403 操作失败
     */
    public function del() {
        $id = $this->id;
        if (!$id) {
            throw new BadRequestException('缺少必要参数',1);
        }
        $model = new MenuDomain();
        $res = $model->delMenu($id);

        return $res;
    }
}
