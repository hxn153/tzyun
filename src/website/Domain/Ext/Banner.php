<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/13
 * Time: 14:07
 */

namespace Website\Domain\Ext;



class Banner
{
    public function getSide(){
        $slide=new \Website\Model\Ext\Banner();
        return $slide->getSlide();
    }

}