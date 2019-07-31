<?php
/**
 * Created by PhpStorm.
 * User: dsx
 * Date: 2019/7/23
 * Time: 17:34
 */

namespace Zstack\Domain\v1;


class VmInstance
{
    /**
     * 根据镜像uuid来获取镜像平台属于Linux还是windows
     * @param $apiAuth
     * @param $uuid
     * @return string
     */
    public function selectPlatformByImagesUuid($apiAuth,$uuid){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $rs = json_decode($curl->get($this->SERVER . '/zstack/v1/images/'.$uuid, $this->outTime));
            foreach($rs as $k=>$v){
                $arr["a"]=$v;
            }
            $a= $arr["a"];

            // 一样的输出
            return json_decode(json_encode($a[0]),true)['platform'];

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }


    }

    /**
     * 根据uuid设置云主机密码
     * @param $apiAuth
     * @param $loginPassword
     * @return mixed|string
     */
    public function setPassword($apiAuth,$uuid,$loginPassword){
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            // 第二个参数为待POST的数据；第三个参数表示登录的头部信息

            $data = array(
                'setVmConsolePassword' => array(
                    'consolePassword' => $loginPassword
                )
            );
            $Oauth = \PhalApi\DI()->cache->get('Login:' . $apiAuth);
            $curl->setHeader(array('Authorization' => $Oauth));
            $upd = new Curl();
            $rs = json_decode($upd->curl_put($this->SERVER . '/zstack/v1/vm-instances/'.$uuid.'/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


}