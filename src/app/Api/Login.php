<?php
namespace App\Api;


use App\Model\AdminAuthGroupAccess;
use App\Model\AdminAuthRule;
use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 用户登录
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */
class Login extends Api {

    public function getRules(){
        return array(
          'index'=>array(
              'username'=>array('name'=>'username','require' =>false,'desc'=>'', 'source' => 'post'),
              'password'=>array('name'=>'password','require' =>false,'desc'=>'', 'source' => 'post'),
          )
        );
    }

    /**
     * 后台账号登录
     * @desc POST请求
     * @return string username 账号
     * @return string password 密码
     *
     */
    public function index() {

        $params = \PhalApi\DI()->request->getAll();
        // $params = \PhalApi\DI()->request->getByRule('POST');
        $username = $params['username'];
        $password = $params['password'];
        if (!$username) {
            throw new BadRequestException('缺少用户名!', 1);
        }
        if (!$password) {
            throw new BadRequestException('缺少密码!', 1);
        } else {
            $password = md5($password);
        }
        $model = new \App\Model\AdminUser();
        $userInfo = $model->getLoginUser($username, $password);

        if ($userInfo) {
            if ($userInfo['status']) {
                //更新用户数据
                $AdminUserData = new \App\Model\AdminUserData();
                $userData = $AdminUserData->getLoginUserData($userInfo['id']);
                $data = [];
                if ($userData) {
                    $data['login_times'] = $userData['login_times'] + 1;
                    $data['last_login_ip'] = \PhalApi\Tool::getClientIp();
                    $data['last_login_time'] = time();
                    $data['head_img'] = $userData['head_img'];
                    $AdminUserData->updateLoginUserData($userInfo['id'], $data);
                } else {
                    $data['login_times'] = 1;
                    $data['uid'] = $userInfo['id'];
                    $data['last_login_ip'] = \PhalApi\Tool::getClientIp();
                    $data['last_login_time'] = time();
                    $data['head_img'] = '';
                    $AdminUserData->insert($data);
                }
                $userInfo['userData'] = $data;
            } else {
                throw new BadRequestException('用户已被封禁，请联系管理员', 1);
            }
        } else {
            throw new BadRequestException('用户名密码不正确', 1);
        }
        $userInfo['access'] = $this->getAccess($userInfo['id']);
        //\PhalApi\DI()->cache->set('Login:userInfo', json_encode($userInfo), 7200);
        $apiAuth = md5(uniqid() . time());
        \PhalApi\DI()->cache->set('Login:' . $apiAuth, json_encode($userInfo), 7200);
        \PhalApi\DI()->cache->set('Login:' . $userInfo['id'], $apiAuth, 7200);

        $userInfo['apiAuth'] = $apiAuth;

        return $userInfo;

    }
    /**
     * 获取用户信息
     * @return mixed
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function getUserInfo() {
        //$model = new \App\Model\AdminUser();
        $ApiAuth = $_SERVER['HTTP_APIAUTH'];
        $userInfo =  \PhalApi\DI()->cache->get('Login:' . $ApiAuth);
        if(!$userInfo){
            exit;
        }
        return json_decode($userInfo,true);
    }
    /**
     * 用户登出
     * @desc get请求
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function logout() {
        $ApiAuth = $_SERVER['HTTP_APIAUTH'];
        \PhalApi\DI()->cache->set('Login:' . $ApiAuth, null);
        return '登出成功';
    }
    /**
     * 获取用户权限数据
     * @ignore
     * @param $uid
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    private function getAccess($uid) {
        $isSupper = \App\isAdministrator($uid);

        if ($isSupper) {
            $access = \PhalApi\DI()->notorm->admin_menu->where('hide = 0')->fetchAll();

            return array_values(array_filter(array_column($access, 'url')));
        } else {
            //普通账号
            $model=new AdminAuthGroupAccess();
            $Rule=new AdminAuthRule();
            $groups = $model->getInfoFromUid( $uid);
            if (isset($groups) && $groups['group_id']) {
                $access = $Rule->getInfoFromGroupId($groups['group_id']);
                return array_values(array_unique(array_column($access, 'url')));
            } else {
                return [];
            }
        }
    }
}
