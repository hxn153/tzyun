<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/24
 * Time: 16:19
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 二层网络资源相关接口
 * Class L2VxlanNetwork
 * @package Zstack\Api\v1
 */

class L2VxlanNetwork extends Api

{
    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }

    public function getRules()
    {
        return array(
            'CreateL2VxlanNetworkPool' => array (
                'name' => array ('name' => 'name','require' => true,'desc' => '资源名称	' ),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => true,'desc' => '区域UUID	' ),
                'physicalInterface' => array ( 'name' => 'physicalInterface','require' => true,'desc' => '物理网卡' )
            ),
            'QueryL2VxlanNetworkPool' => array (
                'uuid'=> array ('name' => 'uuid','require' => false,'desc' => 'VXLAN网络池uuid	' )
            ),
            'CreateL2VxlanNetwork' => array (
                'name'=> array ('name' => 'name','require' => true,'desc' => '资源名称'),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => true,'desc' => '区域UUID	'),
                'poolUuid' => array ( 'name' => 'poolUuid','require' => true,'desc' => 'Vxlan网络资源池uuid' ),
                'physicalInterface' => array ( 'name' => 'physicalInterface','require' => true,'desc' => '物理网卡' )
            ),
            'QueryL2VxlanNetwork' => array (
                'uuid'=> array ('name' => 'uuid','require' => false,'desc' => 'VXLAN网络uuid	' )
            ),
            'CreateL2NoVlanNetwork' => array (
                'name'=> array ('name' => 'name','require' => true,'desc' => '资源名称'),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => true,'desc' => '区域UUID	'),
                'physicalInterface' => array ( 'name' => 'physicalInterface','require' => true,'desc' => '物理网卡' )
            ),
            'CreateL2VlanNetwork' => array (
                'vlan'=> array ('name' => 'vlan','require' => true,'type'=>'int','desc' => '虚拟局域网'),
                'name'=> array ('name' => 'name','require' => true,'desc' => '资源名称'),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => true,'desc' => '区域UUID	'),
                'physicalInterface' => array ( 'name' => 'physicalInterface','require' => true,'desc' => '物理网卡' )
            ),
            'QueryL2VlanNetwork' => array (
                'uuid'=> array ('name' => 'uuid','require' => false,'desc' => '二层VLAN网络uuid	' )
            ),
            'DeleteL2Network' => array (
                'uuid'=> array ('name' => 'uuid','require' => true,'desc' => '二层网络uuid	' )
            ),
            'QueryL2Network' => array (
                'uuid'=> array ('name' => 'uuid','require' => false,'desc' => '二层网络uuid	' )
            ),
            'UpdateL2Network' => array (
                'uuid'=> array ('name' => 'uuid','require' => true,'desc' => '二层网络uuid	' ),
                'name'=> array ('name' => 'name','require' => true,'desc' => '普通二层网络名称	' )
            ),
            'AttachL2NetworkToCluster' => array (
                'l2NetworkUuid'=> array ('name' => 'l2NetworkUuid','require' => true,'desc' => '二层网络uuid	' ),
                'clusterUuid'=> array ('name' => 'clusterUuid','require' => true,'desc' => '集群UUID	' )
            ),
            'DetachL2NetworkFromCluster' => array (
                'l2NetworkUuid'=> array ('name' => 'l2NetworkUuid','require' => true,'desc' => '二层网络uuid	' ),
                'clusterUuid'=> array ('name' => 'clusterUuid','require' => true,'desc' => '集群UUID	' )
            ),
            'CreateVniRange' => array (
                'name'=> array ('name' => 'name','require' => true,'desc' => '资源名称' ),
                'startVni'=> array ('name' => 'startVni','type'=>'int','require' => true,'desc' => '起始Vni' ),
                'endVni'=> array ('name' => 'endVni','type'=>'int','require' => true,'desc' => '结束Vni' ),
                'l2NetworkUuid'=> array ('name' => 'l2NetworkUuid','require' => true,'desc' => '二层Vxlan网络资源池uuid' )
            ),
            'QueryVniRange' => array (
                'uuid'=> array ('name' => 'uuid','require' => false,'desc' => 'vni范围uuid' )
            ),
            'DeleteVniRange' => array (
                'uuid'=> array ('name' => 'uuid','require' => true,'desc' => 'vni范围uuid' )
            ),
            'UpdateVniRange' => array (
                'uuid'=> array ('name' => 'uuid','require' => true,'desc' => 'vni范围uuid' ),
                'name'=> array ('name' => 'name','require' => true,'desc' => 'vni名称' ),

            ),


        );
    }

    /**
     * 创建VXLAN网络池
     * @return mixed|string
     */
    public function CreateL2VxlanNetworkPool(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'zoneUuid' => $this->zoneUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool', json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询VXLAN网络池
     * @return mixed|string
     */
    public function QueryL2VxlanNetworkPool(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
             if(!empty($uuid)){
                 $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/'.$uuid, $this->outTime),true);
                 return $rs;
             }else{
                 $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool', $this->outTime),true);
                 return $rs;
             }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 创建VXLAN网络
     * @return mixed|string
     */
    public function CreateL2VxlanNetwork(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'poolUuid' => $this->poolUuid,
                    'zoneUuid' => $this->zoneUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/vxlan', json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询VXLAN网络
     * @return mixed|string
     */
    public function QueryL2VxlanNetwork(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 创建普通二层网络
     * @return mixed|string
     */
    public function CreateL2NoVlanNetwork(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'zoneUuid' => $this->zoneUuid,
                    'physicalInterface' => $this->physicalInterface,

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/no-vlan', json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 创建二层VLAN网络
     * @return mixed|string
     */
    public function CreateL2VlanNetwork(){
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'vlan' => $this->vlan,
                    'name' => $this->name,
                    'zoneUuid' => $this->zoneUuid,
                    'physicalInterface' => $this->physicalInterface,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/vlan', json_encode($data), $this->outTime));
            if($rs){
                $adress= substr($rs->location,-32);
                $polling="$SERVER/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling,$outTime);
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
     * 查询二层VLAN网络
     * @return mixed|string
     */
    public function QueryL2VlanNetwork(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vlan/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vlan', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 删除二层网络
     * @return mixed|string
     */
    public function DeleteL2Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l2-networks/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询二层网络
     * @return mixed|string
     */
    public function QueryL2Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            if(!empty($uuid)){
                $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/'.$uuid, $this->outTime),true);
                return $rs;
            }else{
                $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks', $this->outTime),true);
                return $rs;
            }

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 更新二层网络
     * @return mixed|string
     */
    public function UpdateL2Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                '对应参数' => array(
                    'name' => $this->name
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/l2-networks/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取二层网络类型
     * @return mixed|string
     */
    public function GetL2NetworkTypes(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/types', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 挂载二层网络到集群
     * @return mixed|string
     */
    public function AttachL2NetworkToCluster(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $l2NetworkUuid=$this->l2NetworkUuid;
            $clusterUuid=$this->clusterUuid;
            $data = array(
                'params' => array(
                    'l2NetworkUuid' =>$l2NetworkUuid,
                    'clusterUuid' =>$clusterUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/'.$l2NetworkUuid.'/clusters/'.$clusterUuid, json_encode($data), $this->outTime));
            if($rs){
                $adress= substr($rs->location,-32);
                $polling="$this->SERVER/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling,$this->outTime);
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
     * 从集群上卸载二层网络
     * @return mixed|string
     */
    public function DetachL2NetworkFromCluster(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息
            $l2NetworkUuid=$this->l2NetworkUuid;
            $clusterUuid=$this->clusterUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l2-networks/'.$l2NetworkUuid.'/clusters/'.$clusterUuid, array('Authorization:' . $Oauth)));
            if($rs){
                $adress= substr($rs->location,-32);
                $polling="$this->SERVER/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling,$this->outTime);
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
     * 创建Vni Range
     * @return mixed|string
     */
    public function CreateVniRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $l2NetworkUuid=$this->l2NetworkUuid;
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'startVni' => $this->startVni,
                    'endVni' => $this->endVni,
                    'l2NetworkUuid' => $l2NetworkUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/'.$l2NetworkUuid.'/vni-ranges', json_encode($data), $this->outTime));
            if($rs){
                $adress= substr($rs->location,-32);
                $polling="$this->SERVER/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling,$this->outTime);
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
     * 查询Vni Range
     * @return mixed|string
     */
    public function QueryVniRange(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/vni-range/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/vni-range', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 删除Vni Range
     * @return mixed|string
     */
    public function DeleteVniRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/vni-ranges/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)),true);
           return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 修改Vni Range
     * @return mixed|string
     */
    public function UpdateVniRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateVniRange' => array(
                    'name' => $this->name
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/l2-networks/vxlan-pool/vni-ranges/'.$uuid, json_encode($data), array('Authorization:' . $Oauth)));
            if($rs){
                $adress= substr($rs->location,-32);
                $polling="$this->SERVER/zstack/v1/api-jobs/$adress";
                $a=true;
                do{
                    $ret=$curl->get($polling,$this->outTime);
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
}