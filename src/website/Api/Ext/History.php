<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 11:45
 */

namespace Website\Api\Ext;


use PhalApi\Api;

/**
 * 发展历程
 * @package App\Api
 */
class History extends Api
{
    /**
     * 获取发展历程
     * @return mixed
     */
 public function getHistory(){
        $model=new \Website\Domain\Ext\History();
     return $model->getHistory();
 }
}