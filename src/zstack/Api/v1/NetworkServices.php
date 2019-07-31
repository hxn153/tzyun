<?php
/**
 * Created by 
 * User: hexingneng
 * Date: 2019/7/25
 * Time: 17:40
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 网络服务接口
 * Class NetworkServices
 * @package Zstack\Api\v1
 */

class NetworkServices extends Api
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






}

