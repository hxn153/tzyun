<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/11
 * Time: 9:51
 */

namespace Zstack\Api\v1;


use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 云主机
 * Class VmInstance
 * @package Zstack\Api\v1
 */

class VmInstance extends Api
{
    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }

    public function getRules()
    {
       return array(
           'CreateVmInstance' => array(
               'name'=> array('name' => 'name', 'require' => true, 'desc' => '云主机名称'),
               'instanceOfferingUuid' => array('name' => 'instanceOfferingUuid', 'require' => true, 'desc' => '计算规格UUID
                指定云主机的CPU、内存等参数'),
               'l3NetworkUuids'=> array('name' => 'l3NetworkUuids', 'require' => true,'type'=>'array', 'desc' => '三层网络UUID列表
                可指定一个或多个三层网络，云主机会在每个三层网络上创建一个网卡。'),
               'imageUuid'=> array('name' => 'imageUuid', 'require' => true, 'desc' => '镜像UUID
                云主机的根云盘会从该字段指定的镜像创建。'),
               'dataDiskOfferingUuids'=>array('name'=>'dataDiskOfferingUuids','require'=>false,'type'=>'array','desc'=>'云盘规格uuid列表，可指定一个或多个'),
               'zoneUuid'=>array('name'=>'zoneUuid','require'=>true,'desc'=>'区域uuid'),
               'loginPassword'=>array('name'=>'loginPassword','require'=>true,'desc'=>'登录密码')


           ),
           'DestroyVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'RecoverVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'ExpungeVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'QueryVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>false,'desc'=>'云主机UUID	' )
           ),
           'StartVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'StopVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' ),
               'type'=>array('name'=>'type','require'=>true,'desc'=>'停止云主机的方式。grace：优雅关机，需要云主机里安装了相关ACPI驱动；
               cold：冷关机，相当于直接断电' )
           ),
           'RebootVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'PauseVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'ResumeVmInstance'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'ReimageVmInstance'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'GetVmMigrationCandidateHosts'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'GetCandidatePrimaryStoragesForCreatingVm'=>array(
               'imageUuid'=>array('name'=>'imageUuid','require'=>true,'desc'=>'镜像UUID	' ),
               'l3NetworkUuids'=>array('name'=>'l3NetworkUuids','require'=>true,'desc'=>'三层网络UUID')
           ),
           'GetCandidateIsoForAttachingVm'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'GetCandidateVmForAttachingIso'=>array(
               'isoUuid'=>array('name'=>'isoUuid','require'=>true,'desc'=>'ISO UUID	' )
           ),
           'AttachIsoToVmInstance'=>array(
               'isoUuid'=>array('name'=>'isoUuid','require'=>true,'desc'=>'ISO UUID	' ),
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'DetachIsoFromVmInstance'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' ),
               'isoUuid'=>array('name'=>'isoUuid','require'=>false,'desc'=>'ISO UUID' )
           ),
           'GetVmAttachableDataVolume'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'GetVmAttachableL3Network'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' )
           ),
           'AttachL3NetworkToVm'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID	' ),
               'l3NetworkUuid'=>array('name'=>'l3NetworkUuid','require'=>true,'desc'=>'三层网络UUID		' )
           ),
           'DetachL3NetworkFromVm'=>array(
               'vmNicUuid'=>array('name'=>'vmNicUuid','require'=>true,'desc'=>'云主机网卡UUID，该网卡所在网络会从云主机卸载掉' )
           ),
           'QueryVmNic'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机网卡UUID' )
           ),
           'SetNicQoS'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机网卡UUID' ),
               'outboundBandwidth'=>array('name'=>'outboundBandwidth','require'=>false,'desc'=>'出流量带宽限制' ),
               'inboundBandwidth'=>array('name'=>'inboundBandwidth','require'=>false,'desc'=>'入流量带宽限制	' )
           ),
           'GetNicQoS'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机网卡UUID' )
           ),
           'DeleteNicQoS'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机网卡UUID' ),
               'direction'=>array('name'=>'direction','require'=>true,'desc'=>'入方向还是出方向(in or out)' )
           ),
           'GetInterdependentL3NetworksImages'=>array(
               'zoneUuid'=>array('name'=>'zoneUuid','require'=>true,'desc'=>'区域UUID。必须指定，以确定三层网络和镜像依赖关系。' )
           ),
           'GetVmConsoleAddress'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID' )
           ),
           'SetVmConsolePassword'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID' ),
               'consolePassword'=>array('name'=>'consolePassword','require'=>true,'desc'=>'密码' ),
               'confirmPassword'=>array('name'=>'confirmPassword','require'=>true,'desc'=>'密码' ),

           ),
           'GetVmConsolePassword'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID' )
           ),
           'DeleteVmConsolePassword'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'云主机UUID' )
           ),
           'selectPlatformByImagesUuid'=>array(
               'uuid'=>array('name'=>'uuid','require'=>true,'desc'=>'镜像uuid' )
           ),
       );

    }



    /**
     * 创建云主机
     * @return mixed|string
     */
    public function CreateVmInstance()
    {
        try {
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
           /* //1.确定创建在哪一个区域，根据拿到的值去判断
            $confirmArea=$this->confirmArea;
            $zone=$this->selectZones($apiAuth,$confirmArea);
            //根据所给的镜像uuid查询属于Linux还是windows
            $platform=new \Zstack\Domain\v1\VmInstance();
            $platform1=$platform->selectPlatformByImagesUuid($apiAuth,$this->imageUuid);

            //判断是否为windows，如果为windows登录名为administrator，并且赠送系统盘80G
            if($platform1 != "Linux"){
                $loginName="administrator";
            }else{
                //若不是windows则登录名为root，赠送系统盘20G
                $loginName="root";
            }*/
          

            //创建云主机需要的数据，rootDiskOfferingUuid(根云盘规格)若为空，则该参数不显示，若不为空则表示镜像格式为iso
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'instanceOfferingUuid' => $this->instanceOfferingUuid,
                    'imageUuid' => $this->imageUuid,
                    'l3NetworkUuids' => $this->l3NetworkUuids,
                    'zoneUuid'=>$this->zoneUuid,
                    'dataDiskOfferingUuids'=>$this->dataDiskOfferingUuids,
                )
            );
            $loginPassword=$this->loginPassword;
            if(preg_match('/^[0-9a-zA-Z!_@#$%^&*]{8,20}$/',$loginPassword)) {
                echo "可以注册！";
            }else{
                return "密码不合法！";
            }
            $curl->setHeader(array('Authorization' => $Oauth));
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/vm-instances', json_encode($data), $this->outTime),true);
            if($rs){
                $adress= substr($rs->location,-32);
                $polling=$this->SERVER."/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling);
                    if(isset(json_decode($ret,true)['inventory'])&&json_decode($ret,true)['inventory']){
                        $a=false;
                    }
                }while($a);
            }
            // 一样的输出
            return  json_decode($ret,true);
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 删除云主机
     * @return mixed|string
     */

    public function DestroyVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 恢复已经删除的云主机
     * @return mixed|string
     */
    public function RecoverVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'recoverVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 彻底删除云主机
     * @return mixed|string
     */
    public  function ExpungeVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', '{"expungeVmInstance":{}}', array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 查询云主机
     * @desc 查询云主机接口，若指定云主机uuid，则查询指定的云主机信息，否则查询全部云主机的信息
     * @return mixed|string
     */
    public function QueryVmInstance(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
              //若指定云主机uuid，则查询指定的云主机的信息，若不指定，则查询全部
             if(!empty($uuid)){
                 $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$uuid, $this->outTime),true);
                 return $rs;
             } else{
                 $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances', $this->outTime),true);
                 return $rs;
             }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 启动云主机
     * @return mixed|string
     */
    public function StartVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'startVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 停止云主机
     * @return mixed|string
     */
    public function StopVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'stopVmInstance' => array(
                    'type' => $this->type

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 重启云主机
     * @return mixed|string
     */
    public function RebootVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'rebootVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 暂停云主机
     * @return mixed|string
     */
    public function PauseVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'pauseVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 恢复暂停的云主机
     * @return mixed|string
     */
    public function ResumeVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'resumeVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }


    /**
     * 重置云主机
     * @return mixed|string
     */
    public function ReimageVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'reimageVmInstance' =>(object)array()
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid = $this->vmInstanceUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询共享磁盘所挂载的云主机
     * @return mixed|string
     */
    public  function QueryShareableVolumeVmInstanceRef(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);

            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/volumes/vm-instances/refs', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 获取可热迁移的物理机列表
     * @return mixed|string
     */
    public function GetVmMigrationCandidateHosts(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid=$this->vmInstanceUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/migration-target-hosts', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 获取可选择的主储存
     * @return mixed|string
     */
    public function GetCandidatePrimaryStoragesForCreatingVm(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $imageUuid=$this->imageUuid;
            $l3NetworkUuids=$this->l3NetworkUuids;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/candidate-storages?imageUuid='.$imageUuid.'&l3NetworkUuids='.$l3NetworkUuids, $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 获取云主机可加载ISO列表
     * @return mixed|string
     */
    public function GetCandidateIsoForAttachingVm(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid=$this->vmInstanceUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/iso-candidates', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 获取ISO可加载云主机列表
     * @return mixed|string
     */
    public function GetCandidateVmForAttachingIso(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $isoUuid=$this->isoUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/images/iso/'.$isoUuid.'/vm-candidates', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 加载iso到云主机
     * @return mixed|string
     */
    public function AttachIsoToVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $vmInstanceUuid=$this->vmInstanceUuid;
            $isoUuid=$this->isoUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/iso/'.$isoUuid,null , $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 卸载云主机上的ISO
     * @return mixed|string
     */
    public function DetachIsoFromVmInstance(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid = $this->vmInstanceUuid;
            $isoUuid = $this->isoUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/iso?isoUuid='.$isoUuid, array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云主机可加载云盘列表
     * @return mixed|string
     */
    public function GetVmAttachableDataVolume(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid=$this->vmInstanceUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/data-volume-candidates', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 获取云主机克加载L3网络列表
     * @return mixed|string
     */
    public function GetVmAttachableL3Network(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmInstanceUuid=$this->vmInstanceUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/l3-networks-candidates', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 加载L3网络到云主机
     * @return mixed|string
     */
    public function AttachL3NetworkToVm(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => (object)array()
            );


            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $vmInstanceUuid=$this->vmInstanceUuid;

            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/vm-instances/'.$vmInstanceUuid.'/l3-networks/'.$l3NetworkUuid, json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 从云主机卸载网络
     * @return mixed|string
     */
    public function DetachL3NetworkFromVm(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $vmNicUuid= $this->vmNicUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vm-instances/nics/'.$vmNicUuid, array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询云主机网卡
     * @return mixed|string
     */
    public function QueryVmNic(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/nics/'.$uuid, $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 设置云主机网卡限速
     * @return mixed|string
     */
    public function SetNicQoS(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                    'setNicQos' => array(
                        'outboundBandwidth' => $this->outboundBandwidth,
                        'inboundBandwidth ' => $this->inboundBandwidth
                    )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取云主机的网卡限速
     * @return mixed|string
     */
    public function GetNicQoS(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/QoS', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 取消云主机网卡限速
     * @return mixed|string
     */
    public function DeleteNicQoS(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $direction=$this->direction;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/nic-qos?direction='.$direction, array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 获取相互依赖的镜像和L3网络
     * @return mixed|string
     */
    public function GetInterdependentL3NetworksImages(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/images-l3networks/dependencies', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 设置云主机控制台密码
     * @return mixed|string
     */
    public function SetVmConsolePassword(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $consolePassword=$this->consolePassword;
            $confirmPassword=$this->confirmPassword;
            if($consolePassword!=$confirmPassword){
                return '两次输入的密码不一致，请重新输入';

            }
            $data = array(
                'setVmConsolePassword' => array(
                    'consolePassword' => $this->consolePassword
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }

    }

    /**
     * 获取云主机的控制台密码
     * @return mixed|string
     */
    public function GetVmConsolePassword(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/console-passwords', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 删除云主机控制台的密码
     * @return mixed|string
     */
    public function DeleteVmConsolePassword(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/console-password', array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

}