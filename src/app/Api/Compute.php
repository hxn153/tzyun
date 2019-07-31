<?php


namespace App\Api;


use PhalApi\Api;

/**
 * 计算
 * Class Compute
 * @package App\Api
 */
class Compute extends Api
{
public function getRules()
{
    return array(
        'importExecl'=>array(
            'number'=>array('name'=>'number','type'=>'array','require' => true,'format'=>'array','desc'=>'基数')
        )
    );
}

    /**
     * 导入结算
     */

    public function importExecl()
    {
        $model=new \App\Domain\Compute();
        return $model->jisuan($this->number);
    }
}
