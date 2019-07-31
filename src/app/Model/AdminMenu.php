<?php
namespace App\Model;

use PhalApi\Exception\BadRequestException;
use PhalApi\Model\NotORMModel as NotORM;

class AdminMenu extends NotORM {

  protected function getTableName($id) {
    return 'admin_menu';
  }
  public function index(){
      return $this->getORM()->order('sort ASC')->fetchAll();
  }
  public function addMenu($params){
    return $this->getORM()->insert($params);
  }

    public function updateMenu($id,$data){
        return $this->getORM()->where('id=?',$id)->update($data);
    }
    public function delMenu($id){
        if($this->getORM()->where('fid=?',$id)->count('id')){
            throw new BadRequestException( '当前菜单存在子菜单,不可以被删除!',2);
        }
        $res=$this->getORM()->where('id=?',$id)->delete();
        if ($res === false) {
            throw new BadRequestException( '操作成功!',3);
        } else {
            return '操作成功';
        }
    }
    //根据路径获取菜单名称
    public function getMenuName($url){

        $ret= $this->getORM()->where($url)->fetchOne();
        return $ret;
    }
}
