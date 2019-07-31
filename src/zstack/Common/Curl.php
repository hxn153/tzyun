<?php


namespace Zstack\Common;


class Curl
{
    /**
     * DELETE请求
     * @param $url
     * @param $header
     * @return bool|string
     */
    function curl_del($url,$header) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        //设置头
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //设置请求头
        curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//SSL认证。
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }
    /**
     * PUT请求
     * @param $url
     * @param $header
     * @return bool|string
     */
    function curl_put($url , $put_data,$header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //定义请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//定义是否直接输出返回流
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //定义请求类型，必须为大写
        //curl_setopt($ch, CURLOPT_HEADER,1); //定义是否显示状态头 1：显示 ； 0：不显示
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义header
        curl_setopt($ch, CURLOPT_POSTFIELDS, $put_data); //定义提交的数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        $res = curl_exec($ch);
        curl_close($ch);//关闭
        return $res;
    }


}
