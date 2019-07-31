<?php
/**
 * Created by
 * User: hexingneng
 * Date: 2019/7/18
 * Time: 14:56
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 区域接口
 * Class Zones
 * @package Zstack\Api\v1
 */

class Zones extends Api
{
    /**
     * 变量初始化
     */
    protected $SERVER;
    protected $outTime;

    public function __construct ()
    {
        $this->SERVER = \PhalApi\DI ()->config->get ('app.__SERVER__');
        $this->outTime = \PhalApi\DI ()->config->get ('app.__OUTTIME__');
    }

    /**
     * 接口参数配置
     */
    public function getRules ()
    {
        return array (
            'selectZonesList' => array(
                //'name' => array('name' => 'name', 'require' => true,'desc' => '资源名称'),
            ),
            'getVmLists' => array (
                'page_num' => array ( 'name' => 'page_num','require' => false,'default' => 1,'desc' => '分页页码' ),
                'page_count' => array ( 'name' => 'page_count','require' => false,'default' => 10,'desc' => '分页每页数量' ),
                'keywords' => array ( 'name' => 'keywords','require' => false,'default' =>"",'desc' => '搜索关键字' ),
                'zoneUuid' => array ( 'name' => 'zoneUuid','require' => false,'desc' => "区域uuid，查询云盘快照uuid时不需要传" ),
                'volumesUuid' => array ( 'name' => 'volumesUuid','require' => false,'desc' => "云盘uuid，仅限查询云盘快照uuid时需要" ),
                'module' => array ( 'name' => 'module','require' => true,'desc' => '模块关键字，vm-instances代表云主机，instance-offerings代表计算规格，images代表镜像，
                 volumes代表云盘，affinity-groups代表亲合组，disk-offerings代表云盘规格，volume-snapshots代表云盘快照' ),
            ),
        );
    }

    /**
     * 获取区域列表接口
     * @desc 主要用于获取区域列表信息
     * @return mixed|string
     */
    public function selectZonesList()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . "/zstack/v1/zones?fields=name,uuid",$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            foreach ($rs as $k => $v) {
                $a[ "a" ] = $v;
            }
            $arr = $a[ "a" ];

            return $arr;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }




    /**
     * 获取列表分页接口
     * @desc 主要用于获取云主机各功能模块列表信息，需要传入三个参数
     * @param page_count 页码数
     * @param page_num 每页个数
     * @param keywords 传入的关键字
     * @param zoneUuid 区域uuid
     * @return mixed|string
     */
    public function getVmLists ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            //实例化公共curl类
            $apiAuth = 'admin';
            $apiAuths=\PhalApi\DI()->cache->get('accountName');
            $limit=$this->page_count;
            $start=($this->page_num-1) * $limit;
            $keywords=$this->keywords;
            $module=$this->module;
            $zoneUuid=$this->zoneUuid;
            $volumesUuid=$this->volumesUuid;
            $a=$this->SERVER . "/zstack/v1/".$module."?";
            $b="sort=-createDate&replyWithCount=true&start=".$start."&limit=".$limit;
            $c="&q=status!=Deleted";
            $d="q=name~=%25".$keywords."%25";
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            if(empty($keywords)){
                if($module == "vm-instances"){
                    $condition="zoneUuid";
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a."q=". $condition."=".$zoneUuid."&".$b."&q=state!=Destroyed";
                }else if($module == "volumes"){
                    $condition="primaryStorage.zone.uuid";
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a."q=". $condition."=".$zoneUuid."&".$b.$c;
                }
                else if ($module == "images"){
                    $condition="backupStorage.zone.uuid";
                    $url=$a."q=". $condition."=".$zoneUuid."&".$b.$c;
                }
                else if($module == "volume-snapshots"){
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$this->SERVER . "/zstack/v1/volume-snapshots/trees?q=volumeUuid=".$volumesUuid;
                }
                else if($module == "instance-offerings"){
                    $url=$a.$b;
                }
                else{
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a.$b;
                }
            }else{
                if($module == "vm-instances") {
                    $condition = "zoneUuid";
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a."q=". $condition."=".$zoneUuid."&".$d."&state!=Destroyed&".$b;
                }else if($module == "volumes"){
                    $condition="primaryStorage.zone.uuid";
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a."q=". $condition."=".$zoneUuid."&".$d.$c."&".$b;
                }
                else if ($module == "images"){
                    $condition="backupStorage.zone.uuid";
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a."q=". $condition."=".$zoneUuid."&".$d.$c."&".$b;
                }
                else if($module == "volume-snapshots"){
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$this->SERVER . "/zstack/v1/volume-snapshots?q=volumeUuid=".$volumesUuid."&".$d."&".$b;
                }
                else{
                    $Oauth = \PhalApi\DI ()->cache->get ('sessionUuid:' . $apiAuths);
                    //获取缓存中的会话uuid
                    $curls->setHeader (array ( 'Authorization' => $Oauth ));
                    $url=$a.$d."&".$b;
                }
            }

            $rs = json_decode ($curls->get ($url,$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息

            return $rs;
        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

}

