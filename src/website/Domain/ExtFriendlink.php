<?php


namespace Website\Domain;




class ExtFriendlink
{
    public function getFriendlinkInfo(){
        $model=new \Website\Model\ExtFriendlink();
        return $model->getFriendlinkInfo();
    }
}
