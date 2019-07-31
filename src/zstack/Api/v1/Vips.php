<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/31
 * Time: 10:59
 */

namespace Zstack\Api\v1;


use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 虚拟Ip相关接口
 * Class Vips
 * @package Zstack\Api\v1
 */

class Vips extends Api
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

    public function getRules()
    {
        return array(
            'CreateVip' => array(
                'name' => array('name' => 'name', 'require' => true,'desc' => '资源名称'),
                'l3NetworkUuid' => array('name' => 'l3NetworkUuid', 'require' => true,'desc' => '三层网络uuid')
            ),
            'DeleteVip' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源的UUID，唯一标示该资源')
            ),
            'QueryVip' => array(
                'uuid' => array('name' => 'uuid', 'require' => false,'desc' => '资源的UUID，唯一标示该资源')
            ),
            'UpdateVip' => array(
                'uuid' => array('name' => 'uuid', 'require' => false,'desc' => '资源的UUID，唯一标示该资源'),
                'name' => array('name' => 'name', 'require' => false,'desc' => '资源的名称')
            ),
            'ChangeVipState' => array(
                'uuid' => array('name' => 'uuid', 'require' => false,'desc' => '资源的UUID，唯一标示该资源'),
                'stateEvent' => array('name' => 'stateEvent', 'require' => false,'desc' => '状态事件,可选值为enable和disable')
            ),
            'GetVipUsedPorts' => array(
                'uuid' => array('name'=> 'uuid', 'require' => true,'desc' => '资源的UUID，唯一标示该资源'),
                'protocol' => array('name'=> 'protocol', 'require' => true,'desc' => '协议,可选值为tcp和udp')

            ),
            'SetVipQos' => array(
                'uuid' => array('name'=> 'uuid', 'require' => true,'desc' => '资源的UUID，唯一标示该资源'),
                'port' => array('name'=> 'port', 'require' => true,'desc' => '端口'),
                'outboundBandwidth' => array('name'=> 'outboundBandwidth', 'require' => false,'desc' => '出流量带宽限制'),
                'inboundBandwidth' => array('name'=> 'inboundBandwidth', 'require' => false,'desc' => '入流量带宽限制')
            ),
            'GetVipQos' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源的UUID，唯一标示该资源')
            ),
            'DeleteVipQos' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '虚拟ip的uuid'),
                'port' => array('name' => 'port', 'require' => false,'desc' => '端口')
            )
        );
    }

    /**
     * 创建虚拟ip
     * @return mixed|string
     */
    public function CreateVip(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'name' => $this->name,
                    'l3NetworkUuid' => $this->l3NetworkUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/vips', json_encode($data), $this->outTime));
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
     * 删除虚拟IP
     * @return mixed|string
     */
    public function DeleteVip(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vips/'.$uuid.'?deleteMode=Permissive', array('Authorization:' . $Oauth)));
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 查询虚拟ip
     * @return mixed|string
     */
    public function QueryVip(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
                if(!empty($uuid)){
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vips/'.$uuid, $this->outTime),true);
                    return $rs;
                }else{
                    $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vips', $this->outTime),true);
                    return $rs;
                }

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }


    /**
     * 更新虚拟ip
     * @return mixed|string
     */
    public function UpdateVip(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'updateVip' => array(
                    'name' => $this->name
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vips/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 更改虚拟ip的启用状态
     * @return mixed|string
     */
    public function ChangeVipState(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'changeVipState' => array(
                    'stateEvent' => $this->stateEvent

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vips/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 获取虚拟IP所有业务端口列表
     * @return mixed|string
     */
    public function GetVipUsedPorts(){
               try {
                   // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
               $curl = new \PhalApi\CUrl(2);
                   // 第二个参数为待POST的数据
               $apiAuth = 'admin';
               $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
               $uuid=$this->uuid;
               //把所有的输入为小写的协议转换为大写
               $protocol=strtoupper($this->protocol);
               $curl->setHeader(array('Authorization' => $Oauth));
               $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vips/'.$uuid.'/usedports?protocol='.$protocol, $this->outTime),true);
                   // 一样的输出
               return $rs;

               } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                   return $ex->getMessage();
               }
    }

    /**
     * 设置虚拟ip限速
     * @return mixed|string
     */
    public function SetVipQos(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息
            $data = array(
                'setVipQos' => array(
                    'port' => $this->port,
                    'outboundBandwidth' => $this->outboundBandwidth,
                    'inboundBandwidth' => $this->inboundBandwidth

                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vips/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)));
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
     * 获取虚拟IP限速
     * @return mixed|string
     */
    public function GetVipQos(){
            try {
                // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
                // 第二个参数为待POST的数据
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid=$this->uuid;
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/vip/'.$uuid.'/vip-qos', $this->outTime),true);
                // 一样的输出
            return $rs;

            } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
                return $ex->getMessage();
            }

    }

    /**
     * 取消虚拟ip限速
     * @return mixed|string
     */
    public function DeleteVipQos(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数判断登录的header信息

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $uuid = $this->uuid;
            $port=$this->port;
            $curl->setHeader(array('Authorization' => $Oauth));
            $del = new Curl();
            $rs = json_decode($del->curl_del($this->SERVER . '/zstack/v1/vips/'.$uuid.'/vip-qos?port='.$port, array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }
}