<?php


namespace Website\Api;

use App\Common\TcEmailSend;
use App\Common\TcSmsSend;
use PhalApi\Api;

/**
 * 公共数据接口
 */
class ExtFriendlink extends Api
{


    public function getRules()
    {
        return array(
            'getBlog' => array(
                'push' => array('name' => 'push', 'default' => 0, 'min' => 0, 'max' => 1, 'source' => 'get', 'desc' => '只获取推荐博客请传入此参数，值为1.反之则不用')
            ),
            'getBlogFormTag' => array(
                'tag' => array('name' => 'tag', 'require' => true, 'desc' => '标签名字，执行模糊查询')
            ),
            'getBlogInfo'=>array(
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'min'=>1,'desc'=>'主键id')
            )
        );
    }

    /**
     * 获取友情链接
     * @exception 400 非法请求，参数传递错误
     * @desc 获取友情链接所有信息
     * @return string data_id 主键
     * @return string name 名称
     * @return string link_url 链接地址
     * @return string sort 排序
     */
    public function getFirendlinkInfo()
    {
        $model = new \Website\Domain\ExtFriendlink();
        return $model->getFriendlinkInfo();
    }

    /**
     * 获取博客数据
     * @desc 如需查询推荐请传入push值1,默认则查询全部
     * @return mixed
     */
    public function getBlog()
    {

//        $Sms=new TcSmsSend();
//        $data['field_name']='第一个';
//        $data['field_value']='第十个';
//        $data['domain']='天智云官网';
//        $Sms->send($data,13388708882,17130);
//        $boby['username']='测试';
//        $boby['name']='订单';
//        $boby['description']='云服务';
//        $boby['productList']='虚拟主机';
//        $to='405181672@qq.com';
//        $subject='关于测试主题';
//        $tem='tc_tzyun_expire';
//        $Email= new TcEmailSend();
//        $Email->sendTemplate($boby,$to,$tem,$subject);
        $model = new \Website\Domain\Blog();
        $ret = $model->getBlog($this->push);
        foreach ($ret as & $v) {
            $v['tag'] = unserialize($v['tag']);
        }
        //\PhalApi\DI()->logger->log('info','博客数据',$ret);
        return $ret;
    }

    /**
     * 根据标签获取相关博客
     * @exception 400 非法请求，参数传递错误
     * @desc 根据标签获取相关博客信息
     */
    public function getBlogFormTag()
    {
        $model = new \Website\Domain\Blog();
        return $model->getBlogFormTag($this->tag);
    }

    /**
     * 获取博客详情
     * 根据主键id获取博客详情
     * @return mixed
     */
    public function getBlogInfo(){
        $model = new \Website\Domain\Blog();
        return $model->getBlogInfo($this->id);
    }


}
