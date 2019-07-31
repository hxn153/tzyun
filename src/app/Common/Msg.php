<?php


namespace App\Common;


use PhalApi\Response;

class Msg extends Response
{
    /**
     * @var int JSON常量组合的二进制掩码
     * @see http://php.net/manual/en/json.constants.php
     */
    protected $options;


    public function __construct($options = 0) {
        $this->options = $options;

        $this->addHeaders('Content-Type', 'application/json;charset=utf-8');
    }

    protected function formatResult($result) {

        return json_encode($result, $this->options);
    }
    public function getResult()
    {
        //\PhalApi\DI()->logger->info('是否為空',$this->msg);
        if(is_array($this->data) ){
            $rs = array(
                'ret'   => $this->ret,
                'data'  => is_array($this->data) && empty($this->data) ? (object)$this->data : $this->data, // # 67 优化
                'msg'   => $this->msg,
            );

            if (!empty($this->debug)) {
                $rs['debug'] = $this->debug;
            }
        }else{
            $rs = array(
                'ret'   => $this->ret,
                //'data'  => is_array($this->data) && empty($this->data) ? (object)$this->data : $this->data, // # 67 优化
                'msg'   => is_array($this->data) && empty($this->data) ? (object)$this->data : $this->data,
            );

            if (!empty($this->debug)) {
                $rs['debug'] = $this->debug;
            }
        }

        return $rs;
    }

}
