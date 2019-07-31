<?php
namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 用户管理接口
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Accounts extends Api {


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
            'createAcc'=>array(
                'name'=>array('name'=>'name','require' =>true,'source'=>'post','desc'=>'账号'),
                'password'=>array('name'=>'password','require' =>true,'source'=>'post','desc'=>'密码'),
            ),
            'queryAcc'=>array(
                'uuid'=>array('name'=>'uuid','require' =>true,'source'=>'get','desc'=>'UUID'),
            ),
            'delAcc'=>array(
                'uuid'=>array('name'=>'uuid','require' =>false,'source'=>'get','desc'=>'UUID'),
            ),
            'editAcc'=>array(
                'name'=>array('name'=>'name','require' =>false,'source'=>'post','desc'=>'新账号'),
                'password'=>array('name'=>'password','require' =>false,'source'=>'post','desc'=>'密码'),
                'uuid'=>array('name'=>'uuid','require' =>false,'source'=>'post','desc'=>'根据UUID'),
            ),
            'getAccountQuotaUsage'=>array(
                'accountUuid'=>array('name'=>'accountUuid','require' =>false,'source'=>'get','desc'=>'账户uuid'),
            ),
            'updateQuota'=>array(
                'identityUuid'=>array('name'=>'identityUuid','require' =>false,'desc'=>'账户uuid'),
                'name'=>array('name'=>'name','require' =>false,'desc'=>'资源名称'),
                'value'=>array('name'=>'value','require' =>false,'desc'=>'配额值'),
            ),
            'queryQuota'=>array(
                'accountUuid'=>array('name'=>'accountUuid','require' =>false,'desc'=>'账户uuid'),
            )
        );

    }
    /**
     *zstack账户创建
     * @desc POST 创建账号操作
     * @return
     * @Exception
     */
    public function createAcc(){
        $model=new \Zstack\Domain\v1\Accounts();
        $params['name']=$this->name;
        $params['password']=$this->password;
        $rs=$model->createAcc($params);
        return $rs;
    }
    /**
     *zstack账户查询
     * @desc GET 查询账号操作
     * @return
     * @Exception
     */
    public function queryAcc(){
        $model=new \Zstack\Domain\v1\Accounts();
        $params['uuid']=$this->uuid;
        $rs=$model->queryAcc($params);
        return $rs;
    }
    /**
     *zstack账户删除
     * @desc GET
     * @return
     * @Exception
     */
    public function delAcc(){
        $model=new \Zstack\Domain\v1\Accounts();
        $params['uuid']=$this->uuid;
        $rs=$model->delAcc($params);
        return $rs;
    }
    /**
    zstack账户更新
     * @desc POST 更新账号操作
     * @return
     * @Exception
     */
    public function editAcc(){
        $model=new \Zstack\Domain\v1\Accounts();
        $params['name']=$this->name;
        $params['password']=$this->password;
        $params['uuid']=$this->uuid;
        $rs=$model->eidtAcc($params);
        return $rs;
    }
    /**
     * zstack账户登录
     * @desc zstack账号登录接口
     * @return string accountName 账号
     * @return string password 密码
     * @return string version 版本，格式：X.X.X
     * @exception 400 非法请求，参数传递错误
     */
    public function login() {
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $data = array(
                'logInByAccount' => array(
                    'accountName' => 'admin',
                    'password' => '98845d31a23642cf9204359b45913b578b8650abc6cf900d4b0543c56567bf5fc864ea182aa6ad879df7353458e1417db9c0d88cfd5c29ac4a41237ebde75eee'
                )
            );
            $rs = json_decode($curl->post($this->SERVER.'/zstack/v1/accounts/login', json_encode($data), $this->outTime));
            // 一样的输出
            $ret=json_decode(json_encode($rs,true),true);
            $OAuth=$ret['inventory']['uuid'];
            $apiAuth='admin';
            \PhalApi\DI()->cache->set('Login:' . $apiAuth, 'OAuth '.$OAuth, 7200);
            return $rs;
            // print_r($rs);
            // return array('title' => 'Hello World in Foo!');
        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 查询获取账户配额使用情况
     * @desc 主要用于获取账户配额使用情况，需要传入一个必要参数
     * @return mixed|string
     */
    public function getAccountQuotaUsage()
    {
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            $accountUuid=$this->accountUuid;
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER. "/zstack/v1/accounts/quota/".$accountUuid."/usages",$this->outTime),true);
            // 发送请求，获取轮询地址

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 查询配额
     * @desc 主要用于查询配额，需要传入一个必要参数
     * @return mixed|string
     */
    public function queryQuota()
    {
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            $accountUuid=$this->accountUuid;
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER. "/zstack/v1/accounts/quota/".$accountUuid."/usages",$this->outTime),true);
            // 发送请求，获取轮询地址

            return $rs;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 更新配额接口
     * @desc 主要用于更新配额，需要传入三个必要参数
     * @return mixed|string
     */
    public function updateQuota(){
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            $curls->setHeader(array('Authorization' => $Oauth));
            //获取缓存中的会话uuid
            $data = array(
                'updateQuota' =>array(
                    "identityUuid" =>$this->identityUuid,
                    "name" => $this->name,
                    "value" => $this->value
                )
            );

            $rs = json_decode($curl->curl_put($this->SERVER . '/zstack/v1/accounts/quotas/actions', json_encode($data), array('Authorization:' . $Oauth)),true);
            // 一样的输出
            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }



}
