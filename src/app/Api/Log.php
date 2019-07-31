<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/12
 *+----------------------------------------------------------------------
 *| Time: 9:10
 *+----------------------------------------------------------------------
 */
namespace App\Api;
use App\Model\AdminLog;
use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 操作日志
 * Class Log
 * @package App\Api
 */
class Log extends Api
{
    public function getRules()
    {
        return array(
            'index'=>array(
                'page'=>array('name'=>'page','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：第几页'),
                'size'=>array('name'=>'size','type'=>'int','source'=>'get','require'=>true,'desc'=>'分页：取几条'),
                'type'=>array('name'=>'type','type'=>'int','source'=>'get','require'=>false,'desc'=>'查询类型'),
                'keywords'=>array('name'=>'keywords','type'=>'string','source'=>'get','require'=>false,'desc'=>'关键字'),
            ),
            'del'=>array(
                'id'=>array('name'=>'id','type'=>'int','source'=>'get','require'=>true,'desc'=>'主键ID'),
            )
        );
    }

    /**
     *获取操作日志列表
     * @desc get请求
     * @return array list 操作数据列表
     * @return array int 共几条
     * @Exception 401  参数错误
     */
    public function index() {
        $data =\PhalApi\DI()->request->getAll();
        if(!$data['page'] || !$data['size']){
            throw new BadRequestException('参数错误',1);
        }
        $obj = new AdminLog();
        $where='';
        if ($data['type']) {
            switch ($data['type']) {
                case 1:
                    $where= "url LIKE '%{$data['keywords']}%'";
                    break;
                case 2:
                    $where= "nickname LIKE '%{$data['keywords']}%'";
                    break;
                case 3:
                    $where= "uid LIKE '%{$data['keywords']}%'";
                    break;
            }
        }
        $listObj = $obj->index($where,$data);
        return array('list'  => $listObj['data'],'count' => $listObj['count']);
    }

    /**
     *删除日志
     * @desc get请求
     * @return string msg 成功
     * @Exception 401 参数异常
     */
    public function del() {
        $id = $this->id;
        if (!$id) {
           throw new BadRequestException('参数错误',1);
        }
        $model=new AdminLog();
        $model->del('id='.$id);
        return '操作成功';

    }

}
