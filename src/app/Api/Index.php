<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/19
 *+----------------------------------------------------------------------
 *| Time: 10:07
 *+----------------------------------------------------------------------
 */


namespace App\Api;


use PhalApi\Api;
use PhalApi\Exception\BadRequestException;

/**
 * 图片上传
 * Class Index
 * @package App\Api
 */
class Index extends Api
{
    public function getRules()
    {
        return array(
          'upload'=>array(
              'file'=>array(
                  'name'=>'file',
                  'type'=>'file',
                  'require'=>true,
                  'source'=>'post',
                  'range' => array('image/jpeg', 'image/png'),
                  'ext' => array('jpg', 'jpeg', 'png', 'bmp')
              )
          )
        );
    }

    public function index() {
        return json_encode(['welcome']);
    }

    /**
     *图片上传
     * @desc POST请求，上传单张图片
     * @return array data fileName(名字) fileUrl(路径)
     * @Exception 401 string 相关图片错误信息
     */
    public function upload() {
        $path = '/upload/' . date('Ymd', time()) . '/';
        $name = $_FILES['file']['name'];
        $tmp_name = $_FILES['file']['tmp_name'];
        $error = $_FILES['file']['error'];
        //过滤错误
        if ($error) {
            switch ($error) {
                case 1 :
                    $error_message = '您上传的文件超过了PHP.INI配置文件中UPLOAD_MAX-FILESIZE的大小';
                    break;
                case 2 :
                    $error_message = '您上传的文件超过了PHP.INI配置文件中的post_max_size的大小';
                    break;
                case 3 :
                    $error_message = '文件只被部分上传';
                    break;
                case 4 :
                    $error_message = '文件不能为空';
                    break;
                default :
                    $error_message = '未知错误';
            }
            throw new BadRequestException($error_message,1);
        }
        $arr_name = explode('.', $name);
        $hz = array_pop($arr_name);
        $new_name = md5(time() . uniqid()) . '.' . $hz;
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0755, true);
        }
        if (move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $path . $new_name)) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            return ['fileName' => $new_name,'fileUrl'  => $http_type.$_SERVER['HTTP_HOST'] . $path . $new_name];
        } else {
            return '文件上传失败';
        }
    }
}
