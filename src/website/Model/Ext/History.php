<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 11:46
 */

namespace Website\Model\Ext;


use PhalApi\Model\NotORMModel;

class History extends NotORMModel
{
    public function getHistory(){
        return $this->getORM()->fetchAll();
    }
}