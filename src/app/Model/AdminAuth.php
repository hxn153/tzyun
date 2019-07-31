<?php


namespace App\Model;



use PhalApi\Model\NotORMModel;

class AdminAuth extends NotORMModel{
    protected function getTableName($id) {
        return 'admin_auth_group';
    }
    public function index($limit,$where){
        $whereAnd="";
        $num=12;
            foreach ($where as $k=>$v){
                if($num===12){
                    $whereAnd.="WHERE ".$k." ".$v;
                }else{
                    $whereAnd.=" AND ".$k." ".$v;
                }
                $num++;
            }
        $sql="SELECT * FROM  admin_auth_group ".$whereAnd. " ORDER BY id DESC limit ".$limit['page'].",".$limit['size'];
        $countSql="SELECT * FROM  admin_auth_group ".$whereAnd. " ORDER BY id DESC";

        $ret= $this->getORM()->queryAll($sql,array());
        $count= $this->getORM()->queryAll($countSql,array());

        return ['data'=>$ret,'count'=>count($count)];
    }
    //插入数据返回新增ID
    //注意，这里不能使用连贯操作，因为要保持同一个ORM实例
    public function add($params){
        $ret=$this->getORM();
        $ret->insert($params);
        return $ret->insert_id();
    }
    public function changeStatus($id,$status){
        return $this->getORM()->where('id=?',$id)->update(['status'=>$status]);
    }
    public function del($id){
        return $this->getORM()->where('id=?',$id)->delete();
    }
    public function edit($id,$data){
        unset($data['id']);
        return $this->getORM()->where('id=?',$id)->update($data);
    }
    public function getInfoFromGroups(){
        return $this->getORM()->where('status=1')->order('id DESC')->fetchAll();
    }
    public function getInfo($where){
        $sql="SELECT * FROM admin_auth_group WHERE id in (".$where.") AND status=1";
        return $this->getORM()->queryAll($sql);
    }

}
