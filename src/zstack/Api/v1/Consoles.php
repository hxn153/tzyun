<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/17
 * Time: 17:31
 */

namespace Zstack\Api\v1;


use PhalApi\Api;

/**
 * 控制台相关接口
 * Class Consoles
 * @package Zstack\Api\v1
 */

class Consoles extends Api
{
    public function __construct ()
    {
        $this->SERVER=\PhalApi\DI()->config->get('app.__SERVER__');
        $this->outTime=\PhalApi\DI()->config->get('app.__OUTTIME__');
    }

    public function getRules()
    {
       return array(
           'ReconnectConsoleProxyAgent'=>array(
               'vmInstanceUuid'=>array('name'=>'vmInstanceUuid','require'=>true,'desc'=>'云主机UUID		')
           )

       );
    }

    /**
     * 请求控制台访问地址
     * @return mixed|string
     */
    public function ReconnectConsoleProxyAgent(){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'params' => array(
                    'vmInstanceUuid' => $this->vmInstanceUuid
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->post($this->SERVER . '/zstack/v1/consoles', json_encode($data), $this->outTime));
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