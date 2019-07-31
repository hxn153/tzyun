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
 * 亲合组接口
 * Class AffinityGroups
 * @package Zstack\Api\v1
 */

class AffinityGroups extends Api
{
    /**
     * 变量初始化
     */
    protected $SERVER;
    protected $outTime;

    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }


    /**
     * 接口参数配置
     */
    public function getRules() {
        return array(
            'createAffinityGroups' => array(
                'name' => array('name' => 'name', 'require' => true,'desc' => '亲合组名称'),
                'policy' => array('name' => 'policy', 'require' => true,'defualt' =>'antiSoft','desc' => '亲合组策略'),
            ),
            'selectAffinityGroups' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '亲合组uuid'),
            ),
            'deleteAffinityGroups' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '亲合组uuid'),
            ),
            'updateAffinityGroups' => array(
                'name' => array('name' => 'name', 'require' => true,'desc' => '名称'),
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源uuid'),
            ),
            'addAffinityGroupsForVM' => array(
                'affinityGroupUuid' => array('name' => 'affinityGroupUuid', 'require' => true,'desc' => '亲合组uuid'),
                'uuid' => array('name' => 'uuid','require' => true,'desc' => '云主机uuid'),
            ),
            'removeAffinityGroups' => array(
                'affinityGroupUuid' => array('name' => 'affinityGroupUuid', 'require' => true,'desc' => '亲合组uuid'),
                'uuid' => array('name' => 'uuid','require' => true,'desc' => '云主机uuid'),
            ),
            'changeAffinityGroupsState' => array(
                'stateEvent' => array( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '事件状态'),
                'uuid' => array('name' => 'uuid','require' => true,'desc' => '亲合组uuid'),
            ),
        );
    }

    /**
     * 创建亲和组接口
     * @desc 主要用于创建亲和组接口，需要传入两个必须参数，亲和组策略参数值只有一个antiSoft
     * @param string name 亲和组名称
     * @param string policy 亲合组策略
     * @return mixed|string
     */
    public function createAffinityGroups(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $data = array(
                'params' => array(
                    'name'=>$this->name,
                    'policy'=> $this->policy  //只有一个可选值antiSoft
                )
            );
            $apiAuth='admin';
            $Oauth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader(array('Authorization'=>$Oauth));
            $rs = json_decode($curl->post($this->SERVER.'/zstack/v1/affinity-groups', json_encode($data), $this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            if($rs){
                $result = $this->getPollResult ($rs,$curl);
            }

            return json_decode ($result);

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 查询亲合组接口
     * @desc 主要用于查询亲和组信息,只需传入亲和组的uuid
     * @param string uuid 亲和组uuid
     * @return mixed|string
     */
    public function selectAffinityGroups(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $uuid=$this->uuid;
            $userName=\PhalApi\DI()->cache->get('accountName');
            $Oauth=\PhalApi\DI()->cache->get('sessionUuid:' . $userName);
            //获取缓存里的会话uuid

            $curl->setHeader(array('Authorization'=>$Oauth));
            $rs = json_decode($curl->get($this->SERVER.'/zstack/v1/affinity-groups/'.$uuid, $this->outTime),true);
            //发送请求，获取轮询地址，第二个参数表示超时时间，单位为毫秒
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 更新亲和组的信息接口
     * @desc 主要用于更新亲和组的信息，需要传递两个必须参数
     * @param string uuid 亲和组uuid
     * @param string name 新的名称
     * @return mixed|string
     */
    public function updateAffinityGroups()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl =new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array(
                "updateAffinityGroup"=>array(
                    "name"=>$this->name,
                )
            );

            $rs = json_decode($curl->curl_put($this->SERVER . "/zstack/v1/affinity-groups/".$uuid."/actions",json_encode($data), array('Authorization:'.$Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 删除亲和组接口
     * @desc 主要用于删除亲合组，只需传递需要删除的亲合组的uuid参数
     * @param string uuid 亲合组uuid
     * @return mixed|string
     */
    public function deleteAffinityGroups()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode($curl->curl_del($this->SERVER . "/zstack/v1/affinity-groups/".$uuid."?deleteMode=Permissive", array('Authorization:'.$Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return json_decode ($result);

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 添加亲合组到云主机接口
     * @desc 主要用于在亲合组上添加云主机，需要传递两个必须参数
     * @param string uuid 云主机uuid
     * @param string stateEvent 亲合组uuid
     * @return mixed|string
     */
    public function addAffinityGroupsForVM()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $data = array (
                'params' =>(object)array(),
            );
            $uuid = $this->uuid;
            $affinityGroupUuid=$this->affinityGroupUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/affinity-groups/".$affinityGroupUuid."/vm-instances/".$uuid, json_encode ($data), array ('Authorization:' . $Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage ();
        }
    }


    /**
     * 从亲合组移除云主机接口
     * @desc 主要用于移除亲合组的云主机，需要传递两个必须参数
     * @param string uuid 云主机uuid
     * @param string affinityGroupUuid 亲合组uuid
     * @return mixed|string
     */
    public function removeAffinityGroups()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类

            $uuid = $this->uuid;
            $affinityGroupUuid=$this->affinityGroupUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_del($this->SERVER . "/zstack/v1/affinity-groups/".$affinityGroupUuid."/vm-instances?uuid=".$uuid, array ('Authorization:' . $Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return json_decode ($result);

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage ();
        }
    }


    /**
     * 改变亲合组使用状态
     * @desc 主要用于更改亲和组的启用状态，需要传递两个必须参数，其中参数stateEvent值为enable是启用，disable是停用
     * @param string uuid 亲和组uuid
     * @param string stateEvent 状态事件
     * @return mixed|string
     */
    public function changeAffinityGroupsState()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $data = array (
                'changeAffinityGroupState' => array (
                    "stateEvent" =>$this->stateEvent,  //只有两个参数，enable表示开启，disable表示不启用
                )
            );
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put($this->SERVER . "/zstack/v1/affinity-groups/".$uuid."/actions",json_encode ($data),array ('Authorization:' . $Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return json_decode ($result);

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage ();
        }
    }


    private function getPollResult ($data_url,$urls){
        $re_arr=parse_url ($data_url->location);
        $res=$this->SERVER.$re_arr["path"];
        //获取轮询地址
        return $urls->get($res,$this->outTime);
        //再次请求轮询地址获取结果
    }
}