<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
        'User.getSign',
        // 以命名空间名称为key
        'Zstack' => array(
            '*.*'
        ),
        'App' => array(
            '*.*'
        ),
        'Website' => array(
            '*.*'
        )
    ),
    /**
     * CORS跨域扩展
     */
    'cors' => array(
        //域名白名单
        'whitelist'   => array(
            'http://localhost:8080',
            'http://lzc.tc.com/'
            // 'http://dev.api.tzyun.com'
        ),
        //header头
        'headers' => array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS', //支持的请求类型
            'Access-Control-Allow-Headers'     => 'version, access-token, user-token, apiAuth, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With',
            'Access-Control-Allow-Credentials' => 'true' //支持cookie
        )
    ),

    'APP_VERSION'           => 'v4.0',
    'APP_NAME'              => '天智云智能运营管理系统',

    //鉴权相关
    'USER_ADMINISTRATOR'    => [1],

    //安全秘钥
    'AUTH_KEY'              => '3edca75d-f8f6-4d14-8a74-c3a9edb06480',

    //后台登录状态维持时间[目前只有登录和解锁会重置登录时间]
    'ONLINE_TIME'           => 7200,
    //AccessToken失效时间
    'ACCESS_TOKEN_TIME_OUT' => 7200,
    'COMPANY_NAME'          => '天成科技开发团队',

    /**
     * 全局路由
     */
    '__SERVER__'=>'http://182.247.245.29:8080',
    /**
     * 全局参数
     * 表示超时时间，单位为毫秒
     */
    '__OUTTIME__'=>3000,
);
