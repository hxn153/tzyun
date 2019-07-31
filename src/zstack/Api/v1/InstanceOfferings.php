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
 * 计算规格接口
 * Class InstanceOfferings
 * @package Zstack\Api\v1
 */

class InstanceOfferings extends Api
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
            'createSpecification' => array(
                'name' => array('name' => 'name', 'require' => true,'desc' => '资源名称'),
                'cpuNum' => array('name' => 'cpuNum', 'require' => true,'desc' => '内核数量'),
                'memorySize' => array('name' => 'memorySize', 'require' => true,'desc' => '内存大小')
            ),
            'selectSpecification' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源uuid'),
            ),
            'deleteSpecification' => array(
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源uuid'),
            ),
            'updateSpecification' => array(
                'name' => array('name' => 'name', 'require' => true,'desc' => '新资源名称'),
                'uuid' => array('name' => 'uuid', 'require' => true,'desc' => '资源uuid'),
            ),
            'changeEnabled' => array(
                'stateEvent' => array('name' => 'stateEvent', 'require' => true,'desc' => '状态事件,可选值为enable,disable'),
                'uuid' => array('name' => 'uuid','require' => true,'desc' => '资源uuid'),
            ),
        );
    }

    /**
     * 创建云主机计算规格接口
     * @desc 主要用于创建云主机计算规格，需要传入三个必须参数，其中内存大小需大于16777216byte
     * @param string name 计算规格名称
     * @param string cpuNum 计算规格核心个数
     * @param string memorySize 计算规格内存大小
     * @return mixed|string
     */
    public function createSpecification(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $data = array(
                'params' => array(
                    'name'=>$this->name,
                    'cpuNum'=> $this->cpuNum,
                    'memorySize'=> $this->memorySize   //字节数必须大于16777216byte
                )
            );
            $apiAuth='admin';
            $Oauth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader(array('Authorization'=>$Oauth));
            $rs = json_decode($curl->post($this->SERVER.'/zstack/v1/instance-offerings', json_encode($data), $this->outTime),true);
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
     * 查询云主机计算规格接口
     * @desc 主要用于查询计算规格的信息,只需传入计算规格的uuid
     * @param string uuid 计算规格uuid
     * @return mixed|string
     */
    public function selectSpecification(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $apiAuth='admin';
            $uuid=$this->uuid;
            $Oauth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader(array('Authorization'=>$Oauth));
            $rs = json_decode($curl->get($this->SERVER.'/zstack/v1/instance-offerings/'.$uuid, $this->outTime),true);
            //发送请求，获取轮询地址，第二个参数表示超时时间，单位为毫秒
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询云主机计算规格列表接口
     * @desc 主要用于查询云主机计算规格列表信息,不用传递参数
     * @return mixed|string
     */
    public function selectInstanceOfferingsList(){
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $apiAuth='admin';
            $Oauth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader(array('Authorization'=>$Oauth));
            $rs = json_decode($curl->get($this->SERVER.'/zstack/v1/instance-offerings', $this->outTime),true);
            //发送请求，获取轮询地址，第二个参数表示超时时间，单位为毫秒
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 更新云主机计算规格的信息接口
     * @desc 主要用于更新计算规格的名称，需要传递两个必须参数
     * @param string uuid 计算规格uuid
     * @param string name 新的名称
     * @return mixed|string
     */
    public function updateSpecification()
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
                "updateInstanceOffering"=>array(
                    "name"=>$this->name,
            )
            );

            $rs = json_decode($curl->curl_put($this->SERVER . "/zstack/v1/instance-offerings/".$uuid."/actions",json_encode($data), array('Authorization:'.$Oauth)),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return json_decode ($result);

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


    /**
     * 删除云主机计算规格接口
     * @desc 主要用于删除指定计算规格，只需传递需要删除的规格的uuid参数
     * @param string uuid 计算规格uuid
     * @return mixed|string
     */
    public function deleteSpecification()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode($curl->curl_del($this->SERVER . "/zstack/v1/instance-offerings/".$uuid."?deleteMode=Permissive", array('Authorization:'.$Oauth)),true);
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
     * 更改云主机计算规格的启用状态接口
     * @desc 主要用于更改云主机计算规格的启用状态，需要传递两个必须参数，其中参数stateEvent值为enable是启用，disable是停用
     * @param string uuid 计算规格uuid
     * @param string stateEvent 状态事件
     * @return mixed|string
     */
    public function changeEnabled ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $data = array (
                'changeInstanceOfferingState' => array (
                    'stateEvent' => $this->stateEvent,
                )
            );
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/instance-offerings/" . $uuid . "/actions", json_encode ($data), array ('Authorization:' . $Oauth)),true);
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