<?php
/**
 * 统一访问入口
 */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: x-tcyun-token,version, access-token, user-token, apiAuth, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With");
header('Access-Control-Allow-Methods: POST,PUT,GET,DELETE');
header('Access-Control-Allow-Credentials: true');
 if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: x-tcyun-token,version, access-token, user-token, apiAuth, User-Agent, Keep-Alive, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With");
    header('Access-Control-Allow-Methods: POST,PUT,GET,DELETE');
    header('Access-Control-Allow-Credentials: true');
     exit;
 }
require_once dirname(__FILE__) . '/init.php';

$pai = new \PhalApi\PhalApi();
$pai->response()->output();


