<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 14:14
 */

namespace Website\Api\Ext;


use PhalApi\Api;

/**
 * 首页轮播
 * Class SlideShow
 * @package App\Api\Ext
 */
class Banner extends Api
{

    /**
     * 获取首页轮播
     * @return mixed
     */
    public  function getSlide(){
        $slide=new \Website\Domain\Ext\Banner();
        return $slide->getSide();
    }

}