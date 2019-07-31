<?php
/**
 *+----------------------------------------------------------------------
 *| Created by PhpStorm
 *+----------------------------------------------------------------------
 *| Author: lzc <405181672@qq.com>
 *+----------------------------------------------------------------------
 *| Date: 2019/7/30
 *+----------------------------------------------------------------------
 *| Time: 17:40
 *+----------------------------------------------------------------------
 */


namespace Website\Common;


use PhalApi\Crypt\MultiMcryptCrypt;

class Crypt
{
    //加密
    public function enCrypt($data,$key='HF#z?8Tc'){
        $mcrypt = new MultiMcryptCrypt('12345678');
        $ret=$mcrypt->encrypt($data,$key);
        return $ret;
    }
    //解密
    public function deCrypt($data,$key='HF#z?8Tc'){
        $mcrypt = new MultiMcryptCrypt('12345678');
        $ret=$mcrypt->decrypt($data,$key);
        return $ret;
    }
}
