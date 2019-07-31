<?php
namespace Website\Api;


use App\Common\TcSmsSend;
use PhalApi\Api;
use PhalApi\Crypt\MultiMcryptCrypt;
use PhalApi\Exception\BadRequestException;
use Website\Common\Crypt;
use Website\Domain\User as DomainUser;
use Website\Model\TzyZstackUsersModel;
use Website\Model\ZtaAccountModel;
use Zstack\Api\v1\Accounts;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

/**
 * 用户模块接口服务
 */
class User extends Api {
    public function getRules() {
        return array(
            'login' => array(
                'username' => array('name' => 'username', 'require' => true, 'min' => 1, 'max' => 50, 'desc' => '用户名','source' => 'post'),
                'password' => array('name' => 'password', 'require' => true, 'min' => 6, 'max' => 20, 'desc' => '密码','source' => 'post'),
            ),
            'getSign' => array(
                'admin'  => array('name' => 'admin','require'=>true,'desc'=>'根据此项生成签名','source' => 'post'),
                'password'  => array('name' => 'password','require'=>true,'desc'=>'根据此项生成签名','source' => 'post'),
            ),
            'captcha' => array(
                'tmp'  => array('name' => 'tmp','require'=>false,'desc'=>'验证码参数','source' => 'post'),
            ),
            'sendSms' => array(
                'mobile'=>array('name' => 'mobile','require'=>true,'desc'=>'手机号','source' => 'post'),
            ),
            'reg' => array(
                'mobile'=>array('name' => 'mobile','require'=>true,'min' => 11, 'max' => 11,'desc'=>'手机号(账号)','source' => 'post'),
                'passwd'=>array('name' => 'passwd','require'=>true,'min' => 6, 'max' => 22,'desc'=>'密码','source' => 'post'),
                'repasswd'=>array('name' => 'repasswd','require'=>true,'min' => 6, 'max' => 22,'desc'=>'确认密码','source' => 'post'),
                'verify'=>array('name' => 'verify','require'=>true,'desc'=>'密码','source' => 'post'),
        )
        );
    }
    /**
     * 登录接口
     * @desc 根据账号和密码进行登录操作
     * @return array data 登录成功
     * @Exception 402 错误信息
     */
    public function login() {
        $username = $this->username;
        $password = $this->password;
        if (!$username) {
            throw new BadRequestException('缺少账号',2);
        }
        if (!$password) {
            throw new BadRequestException('缺少密码',2);
        } else {
            $password = md5($password);
        }
        $userInput = \PhalApi\DI()->request->get('captcha');
//        $builder=$_SESSION["code"];
//
//        if($builder != $userInput) {
//            //用户输入验证码错误
//            return '您输入验证码错误';
//        }
        $model=new \Website\Domain\User();
        $userInfo =$model->getUserLogin($username,$password);

        if (!empty($userInfo)) {
            if (empty($userInfo['state'])) {
                $this->userInfo=$userInfo['uid'];
                //更新用户数据
                $userData['lastip'] =\PhalApi\Tool::getClientIp();
                $userData['lasttime'] = time();
                $model->setUserLogin(array('uid'=>$userInfo['uid']),$userData);

            } else {
                throw new BadRequestException('用户已被封禁，请联系管理员',2);

            }
        } else {
            throw new BadRequestException('用户名密码不正确',2);
        }
        //$this->userInfo=$userInfo;
        $apiAuth = md5(uniqid() . time());
        \PhalApi\DI()->cache->set('Login:' . $apiAuth, json_encode($userInfo), 7200);
        \PhalApi\DI()->cache->set('TzyunUid:' . $apiAuth, $userInfo['uid'], 7200);
        \PhalApi\DI()->cache->set('Login:' . $userInfo['uid'], $apiAuth, 7200);
        $return['apiAuth'] = $apiAuth;
        $return['title'] = '登录成功';
        return $return;
        // return array('token' => self::$USER_MAP[$this->username]['token']);
    }

    /**
     *获取个人信息
     * @desc GET
     * @return array data 个人信息
     * @Exception
     */
    public function getUserInfo(){
        $apiAuth=$_SERVER['HTTP_X_TCYUN_TOKEN'];
        $auth=\PhalApi\DI()->cache->get('Login:' . $apiAuth);
        if(!$auth){
            throw new BadRequestException('apiAuth已过期',1);
        }
        return json_decode($auth,true);
    }

    /**
     *检测账号是否绑定Zstack
     * @desc
     * @return
     * @Exception
     */
    public function isupgrade(){
        $apiAuth=$_SERVER['HTTP_X_TCYUN_TOKEN'];
        $userInfo= \PhalApi\DI()->cache->get('Login:' . $apiAuth);
        $userInfo=json_decode($userInfo,true);
        $model=new \Website\Domain\User();
        $ret=$model->checkGrade($userInfo['uid']);
        if($ret){
            return ['code'=>1];
        }else{
            return ['code'=>2,'msg'=>'该用户未绑定ztack账号'];
        }
    }

    /**
     *生成随机密码
     * $length 密码长度
     */
    private function  getRandPass($length = 6){
        $password = '';
        //将你想要的字符添加到下面字符串中，默认是数字0-9和26个英文字母
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_len = strlen($chars);
        for($i=0;$i<$length;$i++) {
            $loop = mt_rand(0, ($char_len - 1));
            //将这个字符串当作一个数组，随机取出一个字符，并循环拼接成你需要的位数
            $password .= $chars[$loop];
        }
        return $password;

    }
    /**
     *账号升级
     * @desc POST 绑定Zstack账号
     * @return array data Zstack账号数据
     * @Exception 401 缺失请求头X_TCYUN_TOKEN
     * @Exception 402 该账号已绑定
     * @Exception 403 Token已过期
     */
    public function upGrade(){
        $apiAuth=$_SERVER['HTTP_X_TCYUN_TOKEN'];
        if(!$apiAuth){
            throw new BadRequestException('缺失请求头X_TCYUN_TOKEN',401);
        }
        if(!\PhalApi\DI()->cache->get('Login:' . 'admin')){
            $Acc=new Accounts();
            $Acc->login();
        }
        $userInfo= \PhalApi\DI()->cache->get('Login:' . $apiAuth);
        if(!$userInfo){
            throw new BadRequestException('Token已过期',3);
        }
        $userInfo=json_decode($userInfo,true);
        $tzy=new TzyZstackUsersModel();

        if($tzy->getInfoByWhere(['uid'=>$userInfo['uid']])){
            throw new BadRequestException('该账号已绑定',2);
        }
        $model=new \Zstack\Domain\v1\Accounts();
        $data['name']="a".time();
        $data['password']=$this->getRandPass(12);
        $reInfo=$model->createAcc($data);
        if($reInfo){
            //生成唯一关联uuid
            $relevance= md5(uniqid() . time());
            //本地stack账号信息
            $zstack['uuid']=$reInfo['inventory']['uuid'];
            $zstack['localuuid']=$relevance;
            $zstack['accountName']=$reInfo['inventory']['name'];
            $mcrypt = new Crypt();
            $pawd=$mcrypt->enCrypt($data['password']);
            $zstack['password']=$pawd;
            $zstack['type']='normal';
            $zstack['status']='created';
            $zstack['created']=time();
            $zs=new ZtaAccountModel();
            $zs->insert($zstack);
            //关联表写入
            $tzy_zstack['uid']=$userInfo['uid'];
            $tzy_zstack['zstack_uuid']=$relevance;
            $tzy_zstack['status']='created';
            $tzy_zstack['defaultZoneUuid']='[""]';
            $tzy=new TzyZstackUsersModel();
            $tzy->insert($tzy_zstack);
        }
        return $reInfo;

    }

    /**
     * 注册
     * @desc POST
     * @Exception 402 错误信息
     */
    public function  reg(){
        $mobile=$this->mobile;
        $passwd=$this->passwd;
        $repasswd=$this->repasswd;
        $verify=$this->verify;
        $S_verify=\PhalApi\DI()->cache->get('mobile:' . $mobile);
        if(!preg_match("/^1[345789]{1}\d{9}$/",$mobile)){
           throw new BadRequestException('请输入正确的手机号',2);
        }
        if($passwd !=$repasswd){
            throw new BadRequestException('两次密码输入不一致',2);
        }
        if(empty($S_verify)){
            throw new BadRequestException('验证码已过期',2);
        }
        if($S_verify!=$verify){
            throw new BadRequestException('验证码错误',2);
        }
        $data['mobile']=$mobile;
        $data['passwd']=md5($passwd);
        $data['ismobile']=1;
        $model=new \Website\Domain\User();
        return $model->regDomain($data);
    }

    /**
     * 发送手机验证
     * @desc POST
     * @return int code 成功失败
     * @Exception 402 发送频繁，稍后再发
     */
    public function sendSms(){
        $model= new TcSmsSend();
        $mobile=$this->mobile;
        if(\PhalApi\DI()->cache->get('mobile:' . $mobile)){
            throw new BadRequestException('发送频繁，稍后再发',2);
        }
        $key = '';
        $pattern='1234567890';
        for( $i=0; $i<4; $i++ ) {
            $key .= $pattern[mt_rand(0, 9)];
        }
        \PhalApi\DI()->cache->set('mobile:' . $mobile, $key, 120);
        $ret=$model->send(array('Code'=>$key),$mobile,226);
        $resstr=substr_replace($mobile,'****',3,4);
        if($ret){
            return array('code'=>1,'msg'=>'已发送至'.$resstr);
        }else{
            return array('code'=>-1,'msg'=>'网络错误，请稍后再试');
        }

    }

    /**
     *根据token获取用户会话uuid
     * @desc POST 绑定Zstack账号
     * @return array data Zstack账号数据
     * @Exception 401 缺失请求头X_TCYUN_TOKEN
     * @Exception 402 该账号已绑定
     * @Exception 403 Token已过期
     */
    public function getSessionUuid(){
        $model=new DomainUser();
      /*  $apiAuth=$_SERVER['HTTP_X_TCYUN_TOKEN'];
         if(!$apiAuth){
             throw new BadRequestException('缺失请求头X_TCYUN_TOKEN',401);
         }
         if(!\PhalApi\DI()->cache->get('Login:' . 'admin')){
             $Acc=new Accounts();
             $Acc->login();
         }*/
        $apiAuth="4d129f37226b3618b6c2d700f4f77e04";
        $userInfo= \PhalApi\DI()->cache->get('TzyunUid:' . $apiAuth);
        if($userInfo){
            $usermsg=$model->getSessionUuid ($userInfo);
            if($usermsg){
                $mcrypt = new Crypt();
                $password=$usermsg[0]["password"];
                $usermsg[0]["password"]=$mcrypt->deCrypt($password);
            }
        }
        $res=$model->getLoginUuid ($usermsg);
        if($res){
            $ret=json_decode(json_encode($res,true),true);
            $OAuth=$ret['inventory']['uuid'];
            $apiAuth=$usermsg[0]["accountName"];
            \PhalApi\DI()->cache->set('sessionUuid:' . $apiAuth, 'OAuth '.$OAuth, 7200);
            \PhalApi\DI()->cache->set('accountName', $apiAuth, 7200);
        }

        return  \PhalApi\DI()->cache->get('sessionUuid:' . $apiAuth);
        /*if(!$userInfo){
            throw new BadRequestException('Token已过期',3);
        }
        $userInfo=json_decode($userInfo,true);
        $tzy=new TzyZstackUsersModel();

        if($tzy->getInfoByWhere(['uid'=>$userInfo['uid']])){
            throw new BadRequestException('该账号已绑定',2);
        }
        $model=new \Zstack\Domain\v1\Accounts();
        $data['name']="a".time();
        $data['password']=$this->getRandPass(12);
        $reInfo=$model->createAcc($data);
        if($reInfo){
            //生成唯一关联uuid
            $relevance= md5(uniqid() . time());
            //本地stack账号信息
            $zstack['uuid']=$reInfo['inventory']['uuid'];
            $zstack['localuuid']=$relevance;
            $zstack['accountName']=$reInfo['inventory']['name'];
            //mcrypt加密，key：HF#z?8Tc
            $mcrypt = new MultiMcryptCrypt('12345678');
            $key='HF#z?8Tc';
            $pawd=$mcrypt->encrypt($data['password'],$key);
            $zstack['password']=$pawd;
            $zstack['type']='normal';
            $zstack['status']='created';
            $zstack['created']=time();
            $zs=new ZtaAccountModel();
            $zs->insert($zstack);
            //关联表写入
            $tzy_zstack['uid']=$userInfo['uid'];
            $tzy_zstack['zstack_uuid']=$relevance;
            $tzy_zstack['status']='created';
            $tzy_zstack['defaultZoneUuid']='[""]';
            $tzy=new TzyZstackUsersModel();
            $tzy->insert($tzy_zstack);
        }
        return $reInfo;*/

    }

    /**
     * 退出登录
     */
    public function loginout() {
        $ApiAuth = \PhalApi\DI()->request->getHeader('ApiAuth');
        \PhalApi\DI()->cache->delete('Login:' . $ApiAuth);
        \PhalApi\DI()->cache->delete('Login:' . $this->userInfo);
        return array('退出成功');
    }
    /**
     * 获取签名
     * @desc 接口必要参数
     */
    public function getSign(){
        $res=$this->admin.$this->password;
        $sign=md5($res);
        \PhalApi\DI()->cache->set('sign', $sign, 7200);
        return $sign;
    }

    // 验证码生成
    public function captcha()
    {
        session_start();
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = $phrase->build(4);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        //$builder->setBackgroundColor(255, 255, 255);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(6);
        $builder->setMaxFrontLines(8);
        // 可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        // 把内容存入session
        $_SESSION['code']= $phrase;

        ob_end_clean();
        // 生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        //$builder->output();

        $builder->save("code.jpg");


    }


}
