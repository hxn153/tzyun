<?php


namespace App\Model;


use PhalApi\Model\NotORMModel;

class Compute extends NotORMModel

{

        public function jisuan($number){

            ini_set('max_execution_time', '120');
            \PhalApi\DI()->notorm->beginTransaction('db_master');
            $list=$this->getORM()->fetchAll();
            $num=explode(",",$number[0]);
            $count=count($num)-1;
            foreach ($list as $k=>$v){
                $random=mt_rand(0,$count);
                $ret=bcmul($v['data'],$num[$random],4);
                $this->getORM()->where('id',$v['id'])->update(array('ret'=>$ret));
            }
            \PhalApi\DI()->notorm->commit('db_master');
            return '计算完成';
           // return bcmul('290906531521.137',0.9576,4);
        }
}
