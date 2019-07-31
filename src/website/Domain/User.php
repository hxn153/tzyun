<?php


namespace Website\Domain;


use Website\Model\TzyZstackUsersModel;



class User
{
    public function getUserLogin($username,$password){
        $model=new \Website\Model\User();
        if(filter_var($username,FILTER_VALIDATE_EMAIL)){
            return $model->getUserEmail($username,$password);
        }elseif(preg_match("/^1[345789]{1}\d{9}$/",$username)){
            return $model->getUserPhone($username,$password);
        }else{
            return $model->getUserStyle($username,$password);
        }
    }
    public function regDomain($data){
        $model=new \Website\Model\User();


        return $model->regModel($data);
    }
    /**
     * 登录更新数据
     */
    public function setUserLogin($where,$data){
        $model=new \Website\Model\User();
        $model->setUserLogin($where,$data);
    }
    //检测账号是否绑定
    public function checkGrade($uid){
        $model=new TzyZstackUsersModel();
       return $model->checkGrade($uid);
    }

    public function getSessionUuid($useruid){
        $model=new TzyZstackUsersModel();
        return $model->getSessionUuid($useruid);
    }

    public function getLoginUuid(array $usermsg){
        $SERVER= \PhalApi\DI()->config->get('app.__SERVER__');
        $outTime= \PhalApi\DI()->config->get('app.__OUTTIME__');
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            // 第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            $accountName=$usermsg[0]["accountName"];
            $password=hash("sha512", $usermsg[0]["password"]);
            $data = array(
                'logInByAccount' => array(
                    'accountName' => $accountName,
                    'password' => $password
                )
            );
            $rs = json_decode($curl->post($SERVER.'/zstack/v1/accounts/login', json_encode($data), $outTime));
            // 一样的输出

            return $rs;

        } catch (\PhalApi\Exception\InternalServerErrorException $ex) {
            return $ex->getMessage();
        }
    }




}
