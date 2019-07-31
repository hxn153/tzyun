<?php
/**
 * Created by
 * User: hexingneng
 * Date: 2019/7/12
 * Time: 14:56
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 云主机接口
 * Class VmInstances
 * @package Zstack\Api\v1
 */
class VmInstances extends Api
{

    /**
     * 变量初始化
     */
    protected $SERVER;
    protected $outTime;

    public function __construct ()
    {
        $this->SERVER = \PhalApi\DI ()->config->get ('app.__SERVER__');
        $this->outTime = \PhalApi\DI ()->config->get ('app.__OUTTIME__');
    }

    public function getRules ()
    {
        return array (
            'changeSpecification' => array (
                'VMuuid' => array ( 'name' => 'VMuuid','require' => true,'desc' => '云主机uuid' ),
                "instanceOfferingUuid" => array ( 'name' => 'instanceOfferingUuid','require' => true,'desc' => '资源uuid' ),
            ),
            'queryVmCdRom' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => 'CDROMuuid' ),
                "vmInstanceUuid" => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机uuid' ),
            ),
            'createVmCdRom' => array (
                'name' => array ( 'name' => 'name','require' => true,'desc' => 'CDROM 名称' ),
                "vmInstanceUuid" => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机uuid' )
            ),
            'deleteVmCdRom' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => 'CDROMuuid' ),
            ),
            'updateVmCdRom' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => 'CDROMuuid' ),
                'name' => array ( 'name' => 'name','require' => true,'desc' => 'CDROM 名称' )
            ),
            'setVmInstanceDefaultCdRom' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => 'CDROMuuid' ),
                'vmInstanceUuid' => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机uuid' )
            ),
            'setVmCleanTraffic' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => 'CDROMuuid' ),
                'enable' => array ( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '启用状态' )
            ),
            'updateVmNicMac' => array (
                'vmNicUuid' => array ( 'name' => 'vmNicUuid','require' => true,'desc' => '云主机网卡UUID' ),
                'mac' => array ( 'name' => 'mac','require' => true,'desc' => 'mac地址' )
            ),
            'getImageCandidatesForVmToChange' => array (
                'vmInstanceUuid' => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'ChangeVmImage' => array (
                'vmInstanceUuid' => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机UUID' ),
                'imageUuid' => array ( 'name' => 'imageUuid','require' => true,'desc' => '镜像UUID' ),
            ),
            'setVmMonitorNumber' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
                'monitorNumber' => array ( 'name' => 'monitorNumber','require' => true,'desc' => '显示器个数' ),
            ),
            'getVmMonitorNumber' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'setVmRDP' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
                'enable' => array ( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '主机是否被标识为RDP可访问' )
            ),
            'getVmRDP' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'setVmQga' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
                'enable' => array ( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '启用状态' )
            ),
            'getVmQga' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'deleteVmInstanceHaLevel' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'getVmInstanceHaLevel' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'setVmInstanceHaLevel' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
                'level' => array ( 'name' => 'level','require' => true,'desc' => '云主机高可用级别，NeverStop为永不停机，OnHostFailure为物理机发生异常触发高可用' ),
            ),
            'cloneVmInstance' => array (
                'vmInstanceUuid' => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机UUID' ),
                'strategy' => array ( 'name' => 'strategy','require' => true,'desc' => '策略，可选值为InstantStart、JustCreate' ),
                'names' => array ( 'name' => 'names','require' => true,'type' => 'array','format' => 'explode','separator' => ',','desc' => '云主机的名字清单' ),
            ),
            'updateVmInstance' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
                'name' => array ( 'name' => 'name','require' => true,'desc' => '云主机名' ),
            ),
            'getVmCapabilities' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '云主机UUID' ),
            ),
            'deleteVmStaticIp' => array (
                'vmInstanceUuid' => array ( 'name' => 'vmInstanceUuid','require' => true,'desc' => '云主机UUID' ),
                'l3NetworkUuid' => array ( 'name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
            ),
            'getVmList' => array (
                'page_num' => array ( 'name' => 'page_num','require' => true,'default' => 1,'desc' => '分页页码' ),
                'page_count' => array ( 'name' => 'page_count','require' => true,'default' => 1,'desc' => '分页每页数量' ),
                'replyWithCount' => array ( 'name' => 'replyWithCount','require' => true,'type' => 'boolean','default' => TRUE,'desc' => 'replyWithCount' ),
            ),
        );
    }


    /**
     * 更改云主机计算规格接口
     * @desc 主要用于更改云主机的计算规格配置，需要传递两个必须参数
     * @param string VMuuid 云主机uuid
     * @param string instanceOfferingUuid 计算规格uuid
     * @return mixed|string
     */
    public function changeSpecification ()
    {
        $SERVER = \PhalApi\DI ()->config->get ('app.__SERVER__');
        $outTime = \PhalApi\DI ()->config->get ('app.__OUTTIME__');
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $data = array (
                'changeInstanceOffering' => array (
                    "instanceOfferingUuid" => $this->instanceOfferingUuid
                )
            );
            $VMuuid = $this->VMuuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($SERVER . "/zstack/v1/vm-instances/" . $VMuuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if ($rs) {
                $re_arr = parse_url ($rs->location);
                $res = $SERVER . $re_arr["path"];
                //获取轮询地址
                $result = $curls->get ($res,$outTime);
                //再次请求轮询地址获取结果
            }

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 为云主机创建CDROM的接口
     * @desc 主要用于为云主机创建CDROM,需传递两个必须参数
     * @param string name 计算规格名称
     * @param string cpuNum 计算规格核心个数
     * @param string memorySize 计算规格内存大小
     * @return mixed|string
     */
    public function createVmCdRom ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $data = array (
                'params' => array (
                    "name" => $this->name,
                    "vmInstanceUuid" => $this->vmInstanceUuid,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/vm-instances/cdroms',json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            /* if($rs){
                 $result = $this->getPollResult ($rs,$curl);

             }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 删除CDROM接口
     * @desc 主要用于删除CDROM，只需传递需要删除的规格的uuid参数
     * @param string uuid CDROMuuid
     * @return mixed|string
     */
    public function deleteVmCdRom ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_del ($this->SERVER . "/zstack/v1/vm-instances/cdroms/" . $uuid,array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 修改CDROM信息的接口
     * @desc 主要用于修改CDROM的名称，需要传递一个必须参数,其他自选
     * @param string uuid 计算规格uuid
     * @param string name 新的名称
     * @return mixed|string
     */
    public function updateVmCdRom ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "updateInstanceOffering" => array (
                    "name" => $this->name,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/cdroms/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 设置云主机默认CDROM
     * @desc 主要用于设置云主机默认CDROM，需要传递两个必须参数
     * @param string uuid CDROMuuid
     * @param string vmInstanceUuid 云主机uuid
     * @return mixed|string
     */
    public function setVmInstanceDefaultCdRom ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $vmInstanceUuid = $this->vmInstanceUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "setVmInstanceDefaultCdRom" => (object)array ()
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $vmInstanceUuid . "/cdroms/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 查询CDROM清单接口
     * @desc 主要用于查询CDROM清单，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function queryVmCdRom ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . '/zstack/v1/vm-instances/cdroms/' . $uuid,$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 设置云主机防IP欺骗启用状态接口
     * @desc 主要用于设置云主机防IP欺骗启用状态，需要传递两个必须参数
     * @param string uuid CDROMuuid
     * @param boolean enable 启用状态
     * @return mixed|string
     */
    public function setVmCleanTraffic ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "setVmCleanTraffic" => array (
                    "enable" => true
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    //------------------------------------------------------

    /**
     * 更新云主机mac地址接口
     * @desc 主要用于更新云主机mac地址，需要传递两个必须参数
     * @param string vmNicUuid 云主机网卡UUID
     * @param string mac mac地址
     * @return mixed|string
     */
    public function updateVmNicMac ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $vmNicUuid = $this->vmNicUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "updateVmNicMac" => array (
                    "mac" => $this->mac,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/nics/" . $vmNicUuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }
    //---------------------------------------------------------------------------------


    /**
     * 获取候选镜像列表接口
     * @desc 主要用于获取候选镜像列表，可传递一个可选参数vmInstanceUuid
     * @param string vmInstanceUuid 云主机uuid
     * @return mixed|string
     */
    public function getImageCandidatesForVmToChange ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $vmInstanceUuid = $this->vmInstanceUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/" . $vmInstanceUuid . "/image-candidates",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 修改云主机根云盘接口
     * @desc 主要用于修改云主机根云盘，需要传递两个必须参数
     * @param string vmInstanceUuid 云主机UUID
     * @param string imageUuid 镜像UUID
     * @return mixed|string
     */
    public function ChangeVmImage ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $vmInstanceUuid = $this->vmInstanceUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "changeVmImage" => array (
                    "imageUuid" => $this->imageUuid,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $vmInstanceUuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 设置云主机支持的屏幕数接口
     * @desc 主要用于设置云主机支持的屏幕数，需要传递两个必须参数
     * @param string uuid 云主机UUID
     * @param string monitorNumber 显示器个数
     * @return mixed|string
     */
    public function setVmMonitorNumber ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "setVmMonitorNumber" => array (
                    "monitorNumber" => $this->monitorNumber,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 获取云主机支持的屏幕数接口
     * @desc 主要用于获取云主机支持的屏幕数，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function getVmMonitorNumber ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/monitorNumber",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 设置云主机RDP开关状态接口
     * @desc 主要用于设置云主机RDP开关状态，需要传递两个必须参数
     * @param string uuid 云主机UUID
     * @param boolean enable 云主机是否被标识为RDP可访问
     * @return mixed|string
     */
    public function setVmRDP ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "setVmRDP" => array (
                    "enable" => $this->enable,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 获取云主机RDP开关状态接口
     * @desc 主要用于获取云主机RDP开关状态，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function getVmRDP ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/rdp",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 设置云主机Qga接口
     * @desc 主要用于设置云主机Qga，需要传递两个必须参数
     * @param string uuid 云主机UUID
     * @param boolean enable 云主机是否被标识为RDP可访问
     * @return mixed|string
     */
    public function setVmQga ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "setVmQga" => array (
                    "enable" => $this->enable,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 获取云主机Qga接口
     * @desc 主要用于获取云主机Qga，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function getVmQga ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/qga",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }




    /**
     * 获取云主机高可用级别接口
     * @desc 主要用于获取云主机高可用级别，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function getVmInstanceHaLevel ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/" . $uuid . "/ha-levels",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 设置云主机高可用级别接口
     * @desc 主要用于设置云主机高可用级别,需要传入两个必要参数
     * @param string uuid 云主机uuid
     * @param string level 云主机高可用级别：NeverStop为永不停机，OnHostFailure为物理机发生异常触发高可用
     * @return mixed|string
     */
    public function setVmInstanceHaLevel ()
    {
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $uuid = $this->uuid;
            $data = array (
                'params' => array (
                    "level" => $this->level,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/vm-instances/' . $uuid . '/ha-levels',json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            /* if($rs){
                 $result = $this->getPollResult ($rs,$curl);

             }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 克隆云主机到指定物理机接口
     * @desc 主要用于克隆云主机到指定物理机，需要传递两个必须参数
     * @param string vmInstanceUuid 云主机UUID
     * @param string strategy 策略，可选值为InstantStart、JustCreate
     * @param array names 云主机的名字清单
     * @return mixed|string
     */
    public function cloneVmInstance ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $vmInstanceUuid = $this->vmInstanceUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "cloneVmInstance" => array (
                    "strategy" => $this->strategy,
                    "names" => $this->names,
                )
            );
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/" . $vmInstanceUuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 更新云主机信息接口
     * @desc 主要用于更新云主机信息，需要传递两个必须参数
     * @param string uuid 云主机UUID
     * @param array name 云主机名
     * @return mixed|string
     */
    public function updateVmInstance ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "updateVmInstance" => array (
                    "name" => $this->name,
                )
            );
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/vm-instances/".$uuid."/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 获取云主机能力接口
     * @desc 主要用于获取云主机能力，需传递一个必须参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function getVmCapabilities ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/vm-instances/".$uuid."/capabilities",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 删除云主机指定IP接口
     * @desc 主要用于删除云主机三层网络上指定的IP，只需传递需要删除的规格的uuid参数
     * @param string uuid 云主机uuid
     * @return mixed|string
     */
    public function deleteVmStaticIp ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $vmInstanceUuid = $this->vmInstanceUuid;
            $l3NetworkUuid =$this->l3NetworkUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_del ($this->SERVER . "/DELETE zstack/v1/vm-instances/".$vmInstanceUuid."/static-ips?l3NetworkUuid=".$l3NetworkUuid."&deleteMode=Permissive",array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            /*if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }*/

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }





}