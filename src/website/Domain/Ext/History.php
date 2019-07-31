<?php
 namespace Website\Domain\Ext;

/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 11:46
 */
class History
{
    public function getHistory(){
        $model=new \Website\Model\Ext\History();
        return $model->getHistory();
    }
}