<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 14:08
 */

namespace Website\Model\Ext;


use PhalApi\Model\NotORMModel;

class Banner extends NotORMModel
{
    public  function getSlide(){
        return $this->getORM()->fetchAll();
    }
}