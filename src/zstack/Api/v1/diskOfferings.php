<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/7/2
 * Time: 9:00
 */

namespace Zstack\Api\v1;


use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 云盘规格
 * Class diskOfferings
 * @package Zstack\Api\v1
 */

class DiskOfferings extends Api
{
    public function getRules()
    {
        return array(
            'CreateDiskOffering' => array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => '资源名称'),
                'diskSize' => array('name' => 'diskSize', 'require' => true, 'desc' => '云盘大小')
            ),
            'QueryDiskOffering'=>array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源uuid')
            ),
            'UpdateDiskOffering'=>array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => '资源名称'),
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源uuid')

            ),
            'DeleteDiskOffering'=>array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源uuid')
            ),
            'ChangeDiskOfferingState'=>array(
                'stateEvent' => array('name' => 'stateEvent', 'require' => true, 'desc' => '状态事件'),
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源uuid')
            ),
        );
    }

    /**
     * 创建云盘规格
     * @desc 创建云盘规格接口
     * @return string name 资源名称
     * @return long  diskSize 云盘大小
     * @return string version 版本，格式：X.X.X
     * @exception 400 非法请求，参数传递错误
     */
    public function CreateDiskOffering()
    {
        $SERVER = \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime = \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'diskSize' => $this->diskSize
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($SERVER . '/zstack/v1/disk-offerings', json_encode($data), $outTime),true);
            // 一样的输出

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询云盘规格
     * @desc 根据uuid查询云盘规格接口
     * @return string  uuid 资源uuid
     *
     */
    public function QueryDiskOffering(){
        $SERVER = \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime = \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($SERVER . '/zstack/v1/disk-offerings/'.$uuid, $outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 删除云盘规格
     * @desc 删除云盘规格接口
     * @return string uuid 资源uuid
     */
    public function DeleteDiskOffering(){
        $SERVER = \PhalApi\DI()->config->get('app.__SERVER__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del=new Curl();
            $rs = json_decode($del->curl_del($SERVER . '/zstack/v1/disk-offerings/'.$uuid.'?deleteMode=Permissive', array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 更新云盘规格
     * @desc 更新云盘规格接口
     * @return mixed|string
     * @return string name 资源名称
     */
    public function UpdateDiskOffering(){
        $SERVER = \PhalApi\DI()->config->get('app.__SERVER__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateDiskOffering' => array(
                    'name' =>$this->name,
                    'uuid' => $this->uuid

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($SERVER . '/zstack/v1/disk-offerings/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth) ),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 更新云盘规格启用状态
     * @desc更新云盘规格启用状态接口
     * @return  string stateEvent 云盘规格状态事件，可选值为 enable(启用) disable(暂停)
     * @return  string uuid  资源uuid
     */
    public function ChangeDiskOfferingState(){
        $SERVER = \PhalApi\DI()->config->get('app.__SERVER__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'changeDiskOfferingState' => array(
                    'stateEvent' =>$this->stateEvent,
                    'uuid' => $this->uuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($SERVER . '/zstack/v1/disk-offerings/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth) ),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }
}