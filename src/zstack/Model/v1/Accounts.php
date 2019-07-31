<?php


namespace Zstack\Model\v1;



use PhalApi\Model\NotORMModel;
use Zstack\Common\Curl;

class Accounts extends NotORMModel
{
    protected static $SERVER ;
    protected static $outTime;


    /**
     * 创建账户
     */
    public function createAccount($data,$apiAuth='admin'){
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        $curl = new \PhalApi\CUrl(2);

        try {
            $OAuth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            if (empty($OAuth)){
                return 'OAuth已过期';
            }
            $curl->setHeader(
                array(
                    'Authorization'=>$OAuth,
                )
            );
            $rs = json_decode($curl->post($SERVER.'/zstack/v1/accounts',json_encode($data), $outTime));
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
        } catch (\PhalApi\Exception $ex) {
            return $ex->getMessage();
        }
    }
    /**
     * 更新账户
     */
    public function updateAccount($data,$apiAuth='admin'){
        $curl = new \PhalApi\CUrl(2);
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            $OAuth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            if (empty($OAuth)){
                return 'OAuth已过期';
            }
            $herders=array(
                'Authorization:'.$OAuth,
            );
            $model= new Curl();
            $rs=$model->curl_put($SERVER.'/zstack/v1/accounts/'.$data['uuid'],json_encode($data),$herders);
            // 一样的输出
            if($rs){
                $ret=$curl->get('http://182.247.245.29:8080/zstack/v1/api-jobs/d0345d3ddcae485f8170572b15a2b582',$outTime);
            }
            return json_decode($ret);
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }
    /**
     * 删除账户
     */
    public function deleteAccount($uuid,$apiAuth='admin'){

        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            $OAuth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            if (empty($OAuth)){
                return 'OAuth已过期';
            }
            $model= new Curl();
            $rs=$model->curl_del($SERVER.'/zstack/v1/accounts/'.$uuid.'?deleteMode=Permissive',array('Authorization:'.$OAuth));
            // 一样的输出
            return json_decode($rs);
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }
    /**
     * 查询账户
     * @return mixed|string
     */
    public function queryAccount($uuid,$apiAuth='admin'){

        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        $curl = new \PhalApi\CUrl(2);

        try {
            // 一样的输出

            $OAuth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
            if (empty($OAuth)){
                return 'OAuth已过期';
            }
            $curl->setHeader(array('Authorization'=>$OAuth));
            $rs = json_decode($curl->get($SERVER.'/zstack/v1/accounts/'.$uuid, $outTime));
            // 一样的输出
            return $rs;
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }


}
