<?php
/**
 * Created by 
 * User: hexingneng
 * Date: 2019/7/26
 * Time: 17:54
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 安全组接口
 * Class NetworkServices
 * @package Zstack\Api\v1
 */

class SecurityGroups extends Api
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

    /**
     * 接口参数配置
     */
    public function getRules ()
    {
        return array (
            'selectZonesList' => array(
                //'name' => array('name' => 'name', 'require' => true,'desc' => '资源名称'),
            ),
            'getVmLists' => array (
                'page_num' => array ( 'name' => 'page_num','require' => false,'default' => 1,'desc' => '分页页码' ),
                'page_count' => array ( 'name' => 'page_count','require' => false,'default' => 10,'desc' => '分页每页数量' ),
                'keywords' => array ( 'name' => 'keywords','require' => false,'default' =>"",'desc' => '搜索关键字' ),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => true,'desc' => "区域uuid" ),
                'module' => array ( 'name' => 'module','require' => true,'desc' => '模块,instance-offerings代表计算规格，images代表镜像，
                 vm-instances代表与主机，volumes代表云盘，affinity-groups代表亲合组，disk-offerings代表云盘规格' ),
            ),
            'CreateSecurityGroup'=>array(
                'name' => array ( 'name' => 'name','require' => true,'desc' => '资源名称	' ),
                'ipVersion' => array ( 'name' => 'ipVersion ','require' => false,'type'=>'int','desc' => 'ip协议号,可选值4或者6	' ),
            ),
            'DeleteSecurityGroup'=>array(
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '资源的UUID，唯一标示该资源	' )
            ),
            'QuerySecurityGroup'=>array(
                'uuid' => array ( 'name' => 'uuid','require' => false,'desc' => '资源的UUID，唯一标示该资源	' )
            ),
            'UpdateSecurityGroup'=>array(
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '资源的UUID，唯一标示该资源' ),
                'name' => array ( 'name' => 'name','require' => false,'desc' => '资源名称' )
            ),
            'ChangeSecurityGroupState'=>array(
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '资源的UUID，唯一标示该资源' ),
                'stateEvent' => array ( 'name' => 'stateEvent','require' => true,'desc' => '安全组状态 enable或disable' )
            ),
            'AttachSecurityGroupToL3Network'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' ),
                'l3NetworkUuid' => array ( 'name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' )
            ),
            'DetachSecurityGroupFromL3Network'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' ),
                'l3NetworkUuid' => array ( 'name' => 'l3NetworkUuid','require' => true,'desc' => '三层网络UUID' )
            ),
            'GetCandidateVmNicForSecurityGroup'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' )
            ),
            'AddVmNicToSecurityGroup'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' ),
                'vmNicUuids' => array ( 'name' => 'vmNicUuids','require' => true,'type'=>'array','desc' => '云主机网卡的uuid列表	' )
            ),
            'DeleteVmNicFromSecurityGroup'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' ),
                'vmNicUuids' => array ( 'name' => 'vmNicUuids','require' => true,'type'=>'array','desc' => '云主机网卡的uuid列表	' )
            ),
            'AddSecurityGroupRule'=>array(
                'securityGroupUuid' => array ( 'name' => 'securityGroupUuid','require' => true,'desc' => '安全组UUID	' ),
                'type' => array ( 'name' => 'type','require' => true,'desc' => '流量类型	，也就是出方向与入方向，可选值为Ingress（入方向）和Egress（出方向）' ),
                'startPort' => array ( 'name' => 'startPort','require' => true,'desc' => '如果协议是TCP/UDP, 它是端口范围（port range）的起始端口号；
如果协议是ICMP, 它是ICMP类型（type）	' ),
                'endPort' => array ( 'name' => 'endPort','require' => true,'desc' => '如果协议是TCP/UDP, 它是端口范围（port range）的起始端口号；
如果协议是ICMP，它是ICMP类型（type）	' ),
                'protocol' => array ( 'name' => 'protocol','require' => true,'desc' => '流量协议类型	' ),
                'allowedCidr' => array ( 'name' => 'allowedCidr','require' => true,'desc' => '允许的CIDR,根据流量类型的不同, 允许的CIDR有不同的含义。
如果流量类型是Ingress, 允许的CIDR是允许访问虚拟机网卡的源CIDR
如果流量类型是Egress, 允许的CIDR是允许从虚拟机网卡离开并到达的目的地CIDR	' ),
                'remoteSecurityGroupUuids' => array ( 'name' => 'remoteSecurityGroupUuids ','require' => false,'type'=>'array','desc' => '源安全组uuid列表' )
            ),
            'QuerySecurityGroupRule'=>array(
                'uuid' => array ( 'name' => 'uuid','require' => false,'desc' => '规则UUID	' ),
            ),
            

        );
    }

    /**
     * 获取网络服务类型接口
     * @desc 主要用于获取网络服务类型
     * @return mixed|string
     */
    public function getNetworkServiceTypes()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/network-services/types",$this->outTime),true);
            // 发送请求，获取轮询地址

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 查询网络服务模块接口
     * @desc 主要用于查询网络服务模块
     * @return mixed|string
     */
    public function queryNetworkServiceProvider()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/network-services/providers",$this->outTime),true);
            // 发送请求，获取轮询地址

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 查询网络服务与三层网络引用接口
     * @desc 主要用于查询网络服务与三层网络引用
     * @return mixed|string
     */
    public function queryNetworkServiceL3NetworkRef()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/l3-networks/network-services/refs",$this->outTime),true);
            // 发送请求，获取轮询地址

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 挂载网络服务到三层网络的接口
     * @desc 主要用于挂载网络服务到三层网络,需传递两个必须参数
     * @param string name 计算规格名称
     * @param string cpuNum 计算规格核心个数
     * @param string memorySize 计算规格内存大小
     * @return mixed|string
     */
    public function attachNetworkServiceToL3Network ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $l3NetworkUuid=$this->l3NetworkUuid;
            $data = array (
                'params' => array (
                    "networkServices" =>array(
                        
                    ),
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/l3-networks/'.$l3NetworkUuid.'/network-services',json_encode ($data),$this->outTime),true);
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
     * 创建安全组
     * @return mixed|string
     */
    public function CreateSecurityGroup(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'ipVersion' => $this->ipVersion

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/security-groups', json_encode($data), $this->outTime));
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
     * 删除安全组
     * @return mixed|string
     */
    public function DeleteSecurityGroup(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/security-groups/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)));
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
     * 查询安全组
     * @return mixed|string
     */
    public function QuerySecurityGroup(){
          try {
              // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
          $curl = new \PhalApi\CUrl(2);
              // 第二个参数为待POST的数据
          $apiAuth = 'admin';
          $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
          $uuid=$this->uuid;
          $curl->setHeader(array('Authorization' => $Oauth));
              if(!empty($uuid)){
                  $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups/'.$uuid, $this->outTime),true);
                  return $rs;
              }else{
                  $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups', $this->outTime),true);
                  return $rs;
              }

          } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
              return $ex->getMessage();
          }


    }


    /**
     * 更新安全组
     * @return mixed|string
     */
    public function UpdateSecurityGroup(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateSecurityGroup' => array(
                    'name' => $this->name

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/security-groups/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 改变安全组的状态
     * @return mixed|string
     */
    public function ChangeSecurityGroupState(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'changeSecurityGroupState' => array(
                    'stateEvent' => $this->stateEvent
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/security-groups/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 挂载安全组到三层网络
     * @return mixed|string
     */
    public function AttachSecurityGroupToL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => (object)array(

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $securityGroupUuid=$this->securityGroupUuid;
            $l3NetworkUuid=$this->l3NetworkUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/l3-networks/'.$l3NetworkUuid, json_encode($data), $this->outTime));
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
     * 从L3网络卸载安全组
     * @return mixed|string
     */
    public function DetachSecurityGroupFromL3Network(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $securityGroupUuid=$this->securityGroupUuid;
            $l3NetworkUuid=$this->l3NetworkUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/l3-networks/'.$l3NetworkUuid, array('Authorization:' . $Oauth)));
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
     * 获取网卡列表清单
     * @return mixed|string
     */
    public function GetCandidateVmNicForSecurityGroup(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $securityGroupUuid=$this->securityGroupUuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/vm-instances/candidate-nics', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 添加虚拟机网卡到安全组
     * @return mixed|string
     */
    public function AddVmNicToSecurityGroup(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'vmNicUuids' => $this->vmNicUuids
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $securityGroupUuid=$this->securityGroupUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/vm-instances/nics', json_encode($data), $this->outTime));
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 从安全组删除虚拟机网卡
     * @return mixed|string
     */
    public function DeleteVmNicFromSecurityGroup(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $securityGroupUuid=$this->securityGroupUuid;
            $vmNicUuids=$this->vmNicUuids;
            //获取到的网卡的uuid是为数组，需要把数组转换为字符串才能进行拼接
            $vmNicUuid=implode($vmNicUuids);
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/vm-instances/nics?vmNicUuids='.$vmNicUuid, array('Authorization:' . $Oauth)));
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询应用了安全组的网卡列表
     * @return mixed|string
     */
    public function QueryVmNicInSecurityGroup(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups/vm-instances/nics', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 添加安全组规则
     * @return mixed|string
     */
    public function AddSecurityGroupRule(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'rules' => array(array(
                            'type' => $this->type,
                            'startPort' => $this->startPort,
                            'endPort' => $this->endPort,
                            'protocol' => $this->protocol,
                            'allowedCidr' => $this->allowedCidr
                        )

                    )

                    )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $securityGroupUuid=$this->securityGroupUuid;
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/security-groups/'.$securityGroupUuid.'/rules', json_encode($data), $this->outTime));
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
     * 查询安全组规则
     * @return mixed|string
     */
    public function QuerySecurityGroupRule(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups/rules/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/security-groups/rules', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 删除安全组规则
     * @return mixed|string
     */
    public function DeleteSecurityGroupRule(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //规则uuid列表为数组，需转换为字符串
            $ruleUuids = $this->ruleUuids;
            $ruleUuid=implode($ruleUuids);
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/security-groups/rules?ruleUuids='.$ruleUuid, array('Authorization:' . $Oauth)));
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

