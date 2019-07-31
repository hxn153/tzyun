<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/7/4
 * Time: 13:58
 */

namespace Zstack\Api\v1;


use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 云盘相关接口
 * Class DataVolume
 * @package Zstack\Api\v1
 */

class DataVolume extends Api
{
    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }

    public function  getRules()
    {
        return array(
            'CreateDataVolume' => array(
                'name' => array('name' => 'name', 'require' => true, 'desc' => '资源名称'),
                'diskOfferingUuid' => array('name' => 'diskOfferingUuid', 'require' => true, 'desc' => '云盘规格UUid')
            ),
            'DeleteDataVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                'deleteMode' => array('name' => 'deleteMode', 'require' => true, 'desc' => '删除模式(Permissive 或者 Enforcing, 默认 Permissive)')

            ),
            'ExpungeDataVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),
            'RecoverDataVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),

             'ChangeVolumeState' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                 'stateEvent' => array('name' => 'stateEvent', 'require' => true, 'desc' => '云盘状态事件，可选值为 enable(启用) disable(暂停)')
             ),
            'QueryVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),
             'GetVolumeCapabilities' => array(
             'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '云盘的UUID，唯一标示该资源')
            ),
            'SyncVolumeSize' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),
            'ResizeRootVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                'size' => array('name' => 'size', 'require' => true, 'desc' => '扩展后的大小')
            ),
            'ResizeDataVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                'size' => array('name' => 'size', 'require' => true, 'desc' => '扩展后的大小')
            ),
            'UpdateVolume' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                'name' => array('name' => 'name', 'require' => false, 'desc' => '云盘名称')
            ),
            'SetVolumeQoS' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称'),
                'volumeBandwidth' => array('name' => 'volumeBandwidth', 'require' => true, 'desc' => '云盘限速带宽')
            ),
            'GetVolumeQoS' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),
            'DeleteVolumeQoS' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '资源名称')
            ),
            'GetDataVolumeAttachableVm' => array(
                'volumeUuid' => array('name' => 'volumeUuid', 'require' => true, 'desc' => '云盘UUID	')
            ),
            'AttachDataVolumeToVm' => array(
                'volumeUuid' => array('name' => 'volumeUuid', 'require' => true, 'desc' => '云盘UUID	'),
                'vmInstanceUuid' => array('name' => 'vmInstanceUuid', 'require' => true, 'desc' => '云主机UUID')

            ),
            'DetachDataVolumeFromVm' => array(
                'uuid' => array('name' => 'uuid', 'require' => true, 'desc' => '云盘的UUID，唯一标示该资源'),
                'vmUuid' => array('name' => 'vmUuid', 'require' => true, 'desc' => '云主机的UUID')

            ),
            'CreateVolumeSnapshot' => array(
                'volumeUuid' => array('name' => 'volumeUuid', 'require' => true, 'desc' => '云盘的UUID'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '快照名称'),
                'description' => array('name' => 'description', 'require' => true, 'desc' => '快照描述'),

            ),
            'QueryVolumeSnapshot' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	')
            ),

            'QueryVolumeSnapshotTree' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	')
            ),
            'UpdateVolumeSnapshot' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	'),
                'name' => array('name' => 'name', 'require' => true, 'desc' => '快照名称'),
                'description' => array('name' => 'description', 'require' => true, 'desc' => '快照描述'),
            ),
            'DeleteVolumeSnapshot' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	'),
                'deleteMode ' => array('name' => 'deleteMode ', 'require' => false, 'desc' => '删除模式(Permissive 或者 Enforcing, 默认 Permissive)	')
            ),
            'CreateVolumeSnapshotScheduler' => array(
                'volumeUuid'=> array('name'=> 'volumeUuid', 'require' => true, 'desc' => '云盘UUID'),
                'snapShotName' => array('name'=> 'snapShotName', 'require' => true, 'desc' => '定时任务名称'),
                'schedulerName'=> array('name'=> 'schedulerName	', 'require' => true, 'desc' => '定时任务名称	'),
                'type'=> array('name' =>'type', 'require' => true, 'desc' => '定时任务类型，支持simple和cron两种类型'),
            ),
            'RevertVolumeFromSnapshot' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	')
            ),
            'GetVolumeSnapshotSize' => array(
                'uuid'=> array('name' => 'uuid', 'require' => true, 'desc' => '快照uuid	')
            ),





        );
    }

    /**
     * 创建云盘
     * @return mixed|string
     */
    public function CreateDataVolume(){

        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'diskOfferingUuid' => $this->diskOfferingUuid,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/volumes/data', json_encode($data), $this->outTime),true);
            // 一样的输出

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 删除云盘
     * @return mixed|string
     */
    public function DeleteDataVolume(){

        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $deleteMode=$this->deleteMode;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del=new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/volumes/'.$uuid.'?deleteMode='.$deleteMode, array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 彻底删除云盘
     * @return mixed|string
     */

    public function ExpungeDataVolume(){

        try {
            // 第二个参数判断登录的header信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $put=new Curl();
            $rs = json_decode($put->curl_put($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/actions','{"expungeDataVolume":{}}', array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 恢复云盘
     */
    public  function RecoverDataVolume()
    {
        try {
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $put=new Curl();
            $rs = json_decode($put->curl_put($this->SERVER . '/zstack/v1/volumes/' . $uuid . '/actions', '{"recoverDataVolume":{}}',array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 开启或关闭云盘
     * @return mixed|string
     */
    public function ChangeVolumeState(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'changeVolumeState' => array(
                    'stateEvent' =>$this->stateEvent,
                    'uuid' => $this->uuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云盘清单
     * @return mixed|string
     */
    public  function QueryVolume(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/'.$uuid, $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云盘格式
     * @return mixed|string
     */

    public function GetVolumeFormat(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);

            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/formats', $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云盘支持的类型的能力
     * @return mixed|string
     */
    public function GetVolumeCapabilities(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/capabilities', $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 同步云盘大小
     * @return mixed|string
     */
    public function SyncVolumeSize(){

        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/actions','{"syncVolumeSize":{}}',array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 扩展根云盘
     * @return mixed|string
     */
    public function ResizeRootVolume(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'resizeRootVolume' => array(
                    'size' => $this->size
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volumes/resize/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 扩展云盘数据
     * @return mixed|string
     */
    public function ResizeDataVolume(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'resizeRootVolume' => array(
                    'size' => $this->size
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volumes/data/resize/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 修改云盘属性
     * @return mixed|string
     */
    public function UpdateVolume(){

        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateVolume' => array(
                    'name' => $this->name
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 设置云盘限速
     * @return string
     */
    public function SetVolumeQoS(){

        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'setVolumeQoS' => array(
                    "mode"=> "total",
                    'volumeBandwidth' => $this->volumeBandwidth,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $put=new Curl();
            $rs = json_decode($put->curl_put($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/QoS', json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云盘限速
     * @return mixed|string
     */
    public function GetVolumeQoS(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/qos', $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 取消云盘网卡限速
     * @return mixed|string
     */
    public function DeleteVolumeQoS(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del=new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/Qos', array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云盘能否被加载
     * @return mixed|string
     */
    public function GetDataVolumeAttachableVm(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $volumeUuid=$this->volumeUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/'.$volumeUuid.'/candidate-vm-instances', $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 挂载云盘到云主机上
     * @return mixed|string
     */
    public function AttachDataVolumeToVm(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'vmInstanceUuid' => $this->vmInstanceUuid,
                    'volumeUuid' => $this->volumeUuid,
                )
            );
            $apiAuth = 'admin';
            $volumeUuid=$this->volumeUuid;
            $vmInstanceUuid=$this->vmInstanceUuid;
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/volumes/'.$volumeUuid.'/vm-instances/'.$vmInstanceUuid, json_encode($data), $this->outTime),true);
            // 一样的输出

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 从云主机上卸载云盘
     * @return mixed|string
     */
    public function DetachDataVolumeFromVm(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $uuid=$this->uuid;
            $vmUuid=$this->vmUuid;
            $del=new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/volumes/'.$uuid.'/vm-instances'.'?vmUuid='.$vmUuid, array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 从云盘创建快照
     * @return mixed|string
     */
    public function CreateVolumeSnapshot(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'volumeUuid' => $this->volumeUuid,
                    'name' => $this->name,
                    "description" =>$this->description
                )
            );
            $apiAuth = 'admin';
            $volumeUuid=$this->volumeUuid;
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/volumes/'.$volumeUuid.'/volume-snapshots', json_encode($data), $this->outTime),true);
            // 一样的输出

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询云盘快照
     * @return mixed|string
     */
    public function QueryVolumeSnapshot(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volume-snapshots/'.$uuid, $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询快照树
     * @return mixed|string
     */
    public function QueryVolumeSnapshotTree(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volume-snapshots/trees/'.$uuid, $this->outTime),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 更新云盘快照信息
     * @return mixed|string
     */
    public function UpdateVolumeSnapshot(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateVolumeSnapshot' => array(
                    'name' =>$this->name,
                    'uuid' => $this->uuid,
                    "description" =>$this->description
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volume-snapshots/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth) ),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 删除云盘快照
     * @return mixed|string
     */
    public function DeleteVolumeSnapshot(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $uuid=$this->uuid;
            $del=new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/volume-snapshots/'.$uuid.'?deleteMode=Permissive', array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 创建快照的定时任务
     * @return mixed|string
     */
    public function CreateVolumeSnapshotScheduler(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'snapShotName' => $this->snapShotName,
                    'schedulerName' => $this->schedulerName,
                    'type' => $this->type
                )
            );
            $apiAuth = 'admin';
            $volumeUuid=$this->volumeUuid;
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . ' /zstack/v1/volumes/'.$volumeUuid.'/schedulers/creating-volume-snapshots', json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 将云盘回滚至指定快照
     * @return mixed|string
     */
    public  function RevertVolumeFromSnapshot(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/volume-snapshots/'.$uuid.'/actions','{"revertVolumeFromSnapshot":{}}',array('Authorization:'.$Oauth) ),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取快照容量
     * @return mixed|string
     */
    public  function GetVolumeSnapshotSize(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data=array(
                'revertVolumeFromSnapshot'=>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd=new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/volume-snapshots/'.$uuid.'/actions',json_encode($data),array('Authorization:'.$Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

}