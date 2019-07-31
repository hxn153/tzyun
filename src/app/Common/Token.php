<?php


namespace App\Common;


use PhalApi\Filter;
use PhalApi\Exception\BadRequestException;
use PhalApi\Logger;

class Token implements Filter
{
    protected $signName;

    public function __construct($signName = 'sign') {
        $this->signName = $signName;
    }
    public function check() {
        $allParams = \PhalApi\DI()->request->getAll();
        if (empty($allParams[$this->signName])) {
            throw new BadRequestException(\PhalApi\T('缺少'.$this->signName.'参数'), 5);
        }

        $sign = isset($allParams[$this->signName]) ? $allParams[$this->signName] : '';
        //unset($allParams[$this->signName]);

        $expectSign = \PhalApi\DI()->cache->get($this->signName);
        if(empty($expectSign)){
            throw new BadRequestException(\PhalApi\T($this->signName.'已过期'), 2);
        }

        if ($expectSign != $sign) {
            \PhalApi\DI()->logger->debug('Wrong Sign', array('needSign' => $expectSign));
            throw new BadRequestException(\PhalApi\T('wrong sign'), 6);
        }
    }

    protected function encryptAppKey($params) {
        ksort($params);

        $paramsStrExceptSign = '';
        foreach ($params as $val) {
            $paramsStrExceptSign .= $val;
        }

        return md5($paramsStrExceptSign);
    }
}
