<?php
/**
 * DI依赖注入配置文件
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

use PhalApi\Loader;
use PhalApi\Config\FileConfig;
use PhalApi\Logger;
use PhalApi\Logger\FileLogger;
use PhalApi\Database\NotORMDatabase;

// 兼容对postman下raw方式的参数传递 @dogsta
$postRaw = file_get_contents('php://input');
if (!empty($postRaw)) {
    $postRawArr = json_decode($postRaw, true);
    if (!empty($postRawArr) && is_array($postRawArr)) {
        $_REQUEST = array_merge($_REQUEST, $postRawArr);
        $_POST = array_merge($_POST, $postRawArr);
    }
}
/** ---------------- 基本注册 必要服务组件 ---------------- **/

$di = \PhalApi\DI();

// 配置
$di->config = new FileConfig(API_ROOT . '/config');

// 调试模式，$_GET['__debug__']可自行改名
$di->debug = !empty($_GET['__debug__']) ? true : $di->config->get('sys.debug');

// 日记纪录
$di->logger = new FileLogger(API_ROOT . '/runtime', Logger::LOG_LEVEL_DEBUG | Logger::LOG_LEVEL_INFO | Logger::LOG_LEVEL_ERROR);

// 数据操作 - 基于NotORM
//$di->notorm = new NotORMDatabase($di->config->get('dbs'), $di->config->get('sys.notorm_debug'));
$di->notorm = new \App\Common\NewNotORMDatabase($di->config->get('dbs'), $di->config->get('sys.notorm_debug'));

// JSON中文输出
 //$di->response = new \PhalApi\Response\JsonResponse(JSON_UNESCAPED_UNICODE);
 $di->response = new \App\Common\Msg();

/** ---------------- 定制注册 可选服务组件 ---------------- **/

// 签名验证服务
$di->filter = new \App\Common\Token();

// 缓存 - Memcache/Memcached
$di->cache = function () {
    return new \PhalApi\Cache\FileCache(array('path' => API_ROOT . '/runtime', 'prefix' => 'tzyun'));
};

// 支持JsonP的返回
// if (!empty($_GET['callback'])) {
//     $di->response = new \PhalApi\Response\JsonpResponse($_GET['callback']);
// }

// 生成二维码扩展，参考示例：?s=App.Examples_QrCode.Png
// $di->qrcode = function() {
//     return new \PhalApi\QrCode\Lite();
// };

// 注册扩展的追踪器，将SQL写入日志文件
// $di->tracer = function() {
//     return new \App\Common\Tracer();
// };

// 注册CORS跨域扩展
// $di->cors = new \PhalApi\CORS\Lite();

