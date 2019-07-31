<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/26
 * Time: 9:06
 */

namespace Zstack\Api\v1;


use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 三层网络相关接口
 * Class L3Networks
 * @package Zstack\Api\v1
 */

class L3Networks extends Api
{
    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }

    public function getRules()
    {
        return array(
            'CreateL3Network' => array (
                'name' => array ('name' => 'name','require' => true,'desc' => '资源名称	' ),
                'l2NetworkUuid' => array ( 'name' => 'l2NetworkUuid','require' => true,'desc' => '二层网络UUID' ),
                'system'=> array ( 'name' => 'system','type'=>'boolean','require' => false,'desc' => '是否用于系统云主机,true和false' ),
                'category'=> array ('name' => 'category','require' => false,'desc' => '网络类型，需要与system标签搭配使用，system为false时可设置为Public、Private' )

            ),
            'DeleteL3Network' => array (
                'uuid' => array ('name' => 'uuid','require' => true,'desc' => '三层网络uuid' ),
            ),
            'QueryL3Network' => array (
                'uuid' => array ('name' => 'uuid','require' => false,'desc' => '三层网络uuid' ),
            ),

            'ChangeL3NetworkState' => array (
                'uuid' => array ('name' => 'uuid','require' => true,'desc' => '三层网络uuid' ),
                'stateEvent' => array ('name' => 'stateEvent','require' => true,'desc' => '状态，可选enable与disable' )
            ),
            'GetL3NetworkDhcpIpAddress' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络uuid' ),
            ),
            'RemoveDnsFromL3Network' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络uuid' ),
                'dns' => array ('name' => 'dns','require' => true,'desc' => 'DNS地址	' )
            ),
            'AddHostRouteToL3Network' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络uuid' ),
                'prefix' => array ('name' => 'prefix','require' => true,'desc' => '' ),
                'nexthop' => array ('name' => 'nexthop','require' => true,'desc' => '	' )
            ),
            'GetFreeIp' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => false,'desc' => '三层网络uuid' ),
                'ipRangeUuid' => array ('name' => 'ipRangeUuid','require' => false,'desc' => 'IP段UUID' )
            ),
            'CheckIpAvailability' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => false,'desc' => '三层网络uuid' ),
                'ip' => array ('name' => 'ip','require' => false,'desc' => 'IP地址' )
            ),
            'GetIpAddressCapacity' => array (
                'zoneUuids' => array ('name' => 'zoneUuids','type'=>'array','require' => false,'desc' => '区域UUID' ),
                'l3NetworkUuids' => array ('name' => 'l3NetworkUuids ','type'=>'array','require' => false,'desc' => '三层网络UUID' ),
                'ipRangeUuids' => array ('name' => 'ipRangeUuids','type'=>'array','require' => false,'desc' => 'IP地址范围UUID' )
            ),
            'AddIpRange' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
                'name' => array ('name' => 'name','require' => true,'desc' => '三层网络的名称	'),
                'startIp' => array ('name' => 'startIp','require' => true,'desc' => '起始地址'),
                'endIp' => array ('name' => 'endIp','require' => true,'desc' => '结束地址'),
                'netmask' => array ('name' => 'netmask','require' => true,'desc' => '网络掩码'),
                'gateway' => array ('name' => 'gateway','require' => true,'desc' => '网关')
            ),
            'DeleteIpRange' => array (
                'uuid' => array ('name' => 'uuid','require' => true,'desc' => '资源的UUID，唯一标示该资源	' ),
            ),
            'QueryIpRange' => array (
                'uuid' => array ('name' => 'uuid','require' => false,'desc' => '资源的UUID，唯一标示该资源	' ),
            ),
            'UpdateIpRange' => array (
                'uuid' => array ('name' => 'uuid','require' => false,'desc' => '资源的UUID，唯一标示该资源	' ),
                'name' => array ('name' => 'name','require' => false,'desc' => '资源名称	' ),
            ),
            'AddIpRangeByNetworkCidr' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
                'name' => array ('name' => 'name','require' => true,'desc' => '资源名称	' ),
                'networkCidr' => array ('name' => 'networkCidr','require' => true,'desc' => '网络CIDR' ),
            ),
            'GetL3NetworkMtu' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' )
            ),
            'SetL3NetworkMtu' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
                'mtu' => array ('name' => 'mtu','require' => true,'desc' => '三层网络mtu' )
            ),
            'GetL3NetworkRouterInterfaceIp' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' )
            ),
            'SetL3NetworkRouterInterfaceIp' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
                'routerInterfaceIp' => array ('name' => 'routerInterfaceIp','require' => true,'desc' => '路由器ip' )
            ),
            'AddIpv6Range' => array (
                'l3NetworkUuid' => array ('name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' ),
                'name' => array ('name' => 'name','require' => true,'desc' => '三层网络的名称	'),
                'startIp' => array ('name' => 'startIp','require' => true,'desc' => '起始地址'),
                'endIp' => array ('name' => 'endIp','require' => true,'desc' => '结束地址'),
                'gateway' => array ('name' => 'gateway','require' => true,'desc' => '网关'),
                'prefixLen' => array ('name' => 'prefixLen','type'=>'int','require' => true,'desc' => '前缀长度	'),
                'addressMode' => array ('name' => 'addressMode','require' => true,'desc' => 'IPv6地址分配模式,可选值SLAAC,Stateful-DHCP,Stateless-DHCP')
            ),
            'QueryIpAddress' => array (
                'uuid' => array ('name' => 'uuid','require' => false,'desc' => '资源的UUID，唯一标示该资源	' ),
            ),

        );
    }

    /**
     * 创建三层网络
     * @return mixed|string
     */
    public function CreateL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'l2NetworkUuid' => $this->l2NetworkUuid,
                    'system ' => $this->system ,
                    'category' => $this->category

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks', json_encode($data), $this->outTime));
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
     * 删除三层网络
     * @return mixed|string
     */
    public function DeleteL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l3-networks/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)));

            return  $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询三层网络
     * @return mixed|string
     */
    public function QueryL3Network(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 更新三层网络
     * @return mixed|string
     */
    public function UpdateL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateL3Network' => array(
                    'name' => $this->name
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/l3-networks/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 获取三层网络类型
     * @return mixed|string
     */
    public function GetL3NetworkTypes(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/types', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }
    }

    /**
     * 改变三层网络状态
     * @return mixed|string
     */
    public function ChangeL3NetworkState(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'changeL3NetworkState' => array(
                    'stateEvent' => $this->stateEvent
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/l3-networks/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 获取网络DHCP服务所用地址
     * @return mixed|string
     */
    public function GetL3NetworkDhcpIpAddress(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/dhcp-ip', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 向三层网络添加DNS
     * @return mixed|string
     */
    public function AddDnsToL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'dns' => $this->dns
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/dns', json_encode($data), $this->outTime));
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
     * 从三层网络移除DNS
     * @return mixed|string
     */
    public function RemoveDnsFromL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid = $this->l3NetworkUuid;
            $dns=$this->dns;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/dns/'.$dns, array('Authorization:' . $Oauth)));
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
     * 向三层网络添加主机路由
     * @return mixed|string
     */
    public function AddHostRouteToL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                        'prefix' => $this->prefix,
                        'nexthop' => $this->nexthop
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/hostroute', json_encode($data), $this->outTime));
          return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 获取空闲ip
     * @return mixed|string
     */
    public function GetFreeIp(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $ipRangeUuid=$this->ipRangeUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($l3NetworkUuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/ip/free', $this->outTime),true);
                    return $rs;
                }
                if(!empty($ipRangeUuid))
                {
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/ip-ranges/'.$ipRangeUuid.'/ip/free', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 检查ip的可用性
     * @return mixed|string
     */
    public function CheckIpAvailability(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $ip=$this->ip;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/ip/'.$ip.'/availability', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 添加ip地址范围
     * @return mixed|string
     */
    public function AddIpRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'startIp' => $this->startIp,
                    'endIp' => $this->endIp,
                    'netmask' => $this->netmask,
                    'gateway' => $this->gateway,
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/ip-ranges', json_encode($data), $this->outTime),true);
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 删除IP地址范围
     * @return mixed|string
     */
    public function DeleteIpRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/l3-networks/ip-ranges/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)));
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询IP地址范围
     * @return mixed|string
     */
    public function QueryIpRange(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/ip-ranges/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/ip-ranges', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 更新ip地址范围
     * @return mixed|string
     */
    public function UpdateIpRange(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateIpRange' => (object)array(
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/l3-networks/ip-ranges/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 通过网络CIDR添加id地址范围
     *
     * @return mixed|string
     */
    public function AddIpRangeByNetworkCidr(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'networkCidr'=>$this->networkCidr
                )
            );
            $l3NetworkUuid=$this->l3NetworkUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/ip-ranges/by-cidr', json_encode($data), $this->outTime));
           return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 获取三层网络Mtu值
     * @return mixed|string
     */
    public function GetL3NetworkMtu(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/mtu', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 设置三层网络Mtu值
     * @return mixed|string
     */
    public function SetL3NetworkMtu(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'mtu' => $this->mtu
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/mtu', json_encode($data), $this->outTime),true);
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 获取三层网络上路由器的接口地址
     * @return mixed|string
     */
    public function GetL3NetworkRouterInterfaceIp(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/router-interface-ip', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 设置三层网络路由器接口ip
     * @return mixed|string
     */
    public function SetL3NetworkRouterInterfaceIp(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'routerInterfaceIp' => $this->routerInterfaceIp
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/router-interface-ip', json_encode($data), $this->outTime));
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 添加ip6地址范围
     * @return mixed|string
     */
    public function AddIpv6Range(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'startIp' => $this->startIp,
                    'endIp' => $this->endIp,
                    'gateway' => $this->gateway,
                    'prefixLen' => $this->prefixLen,
                    'addressMode' => $this->addressMode

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/ipv6-ranges', json_encode($data), $this->outTime));
           /* if($rs){
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
            return  json_decode($ret,true);*/
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 查询ip地址
     * @return mixed|string
     */
    public function QueryIpAddress(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/ip-address/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/l3-networks/ip-address', $this->outTime),true);
                    return $rs;
                }
            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }
    }

}