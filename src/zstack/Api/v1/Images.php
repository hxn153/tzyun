<?php
/**
 * Created by
 * User: hexingneng
 * Date: 2019/7/12
 * Time: 14:04
 */

namespace Zstack\Api\v1;

use PhalApi\Api;
use Zstack\Common\Curl;

/**
 * 镜像接口
 * Class Images
 * @package Zstack\Api\v1
 */

class Images extends Api
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
            'deleteImages' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
            ),
            'addImages' => array (
                'name' => array ( 'name' => 'name','require' => true,'desc' => '镜像名称' ),
                'url' => array ( 'name' => 'url','require' => true,'desc' => '被添加镜像的URL地址' ),
                'mediaType' => array ( 'name' => 'mediaType','require' => false,'desc' => '镜像类型' ),
                'system' => array ( 'name' => 'system','require' => false,'desc' => '是否系统镜像' ),
                'format' => array ( 'name' => 'format','require' => true,'desc' => '镜像格式' ),
                'platform' => array ( 'name' => 'platform','require' => false,'desc' => '镜像系统平台' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','require' => true,'desc' => '镜像服务器UUID列表' )
            ),
            'packImages' => array (
                'imageUuid' => array ( 'name' => 'imageUuid','require' => true,'desc' => '镜像uuid' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','require' => true,'type' => 'array','format' => 'explode','separator' => ',','desc' => '镜像服务器UUID列表' ),
            ),
            'selectImages' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
            ),
            'recoverImage' => array (
                'imageUuid' => array ( 'name' => 'imageUuid','require' => true,'desc' => '镜像uuid' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','require' => true,'type' => 'array','format' => 'explode','separator' => ',','desc' => '镜像服务器UUID列表' ),
            ),
            'updateImages' => array (
                'platform' => array ( 'name' => 'platform','require' => true,'desc' => '镜像的系统平台' ),
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
            ),
            'changeImagesState' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
                'stateEvent' => array ( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '事件状态' )
            ),
            'syncImageSize' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
                //'syncImageSize' => array('name' => 'syncImageSize','format' => 'json','require' => true,'desc' => '镜像uuid'),
            ),
            'getImagesServerCandidate' => array (
                'volumeUuid' => array ( 'name' => 'volumeUuid','require' => true,'desc' => '镜像uuid' ),
            ),
            'createRootVolumeFromImages' => array (
                'name' => array ( 'name' => 'name','require' => true,'desc' => '根云盘镜像名称' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','type' => 'array','format' => 'explode','separator' => ',','require' => false,'desc' => '镜像服务器UUID列表' ),
                'platform' => array ( 'name' => 'platform','require' => false,'desc' => '根云盘镜像对应的系统平台' ),
                'system' => array ( 'name' => 'system','require' => false,'desc' => '是否系统根云盘镜像' ),
                'rootVolumeUuid' => array ( 'name' => 'rootVolumeUuid','require' => true,'desc' => '是否系统根云盘镜像' ),
            ),
            'createRootVolumeImages' => array (
                'name' => array ( 'name' => 'name','require' => true,'desc' => '根云盘镜像名称' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','type' => 'array','format' => 'explode','separator' => ',','require' => false,'desc' => '镜像服务器UUID列表' ),
                'system' => array ( 'name' => 'system','require' => false,'desc' => '是否系统根云盘镜像' ),
                'snapshotUuid' => array ( 'name' => 'snapshotUuid','require' => true,'desc' => '是否系统根云盘镜像' ),
            ),
            'createDataVolumeImages' => array (
                'name' => array ( 'name' => 'name','require' => true,'desc' => '数据云盘镜像名称' ),
                'backupStorageUuids' => array ( 'name' => 'backupStorageUuids','type' => 'array','format' => 'explode','separator' => ',','require' => false,'desc' => '镜像服务器UUID列表' ),
                'volumeUuid' => array ( 'name' => 'volumeUuid','require' => true,'desc' => '起始云盘UUID' ),
            ),
            'getImageQga' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
            ),
            'setImageQga' => array (
                'uuid' => array ( 'name' => 'uuid','require' => true,'desc' => '镜像uuid' ),
                'enable' => array ( 'name' => 'enable','type' => 'boolean','default' => TRUE,'require' => true,'desc' => '镜像uuid' ),
            ),
        );
    }

    /**
     * 创建镜像接口
     * @desc 主要用于创建镜像，需要传入四个必须参数
     * @param string name 镜像名称
     * @param string url 被添加镜像的URL地址
     * @param string mediaType 镜像类型
     * @param string system 是否系统镜像
     * @param string format 镜像格式
     * @param string platform 镜像系统平台
     * @param string backupStorageUuids 镜像服务器UUID列表
     * @return mixed|string
     */
    public function addImages ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $data = array (
                'params' => array (
                    "name" => $this->name,
                    "url" => $this->url,  //被添加镜像的URL地址
                    "mediaType" => $this->mediaType,  //镜像类型，分为RootVolumeTemplate，ISO，DataVolumeTemplate
                    "system" => $this->system,   //是否系统镜像
                    "format" => $this->format, //镜像格式
                    "platform" => $this->platform,  //镜像系统平台，分为Linux，Windows，WindowsVirtio，Other，Paravirtualization
                    "backupStorageUuids" => $this->backupStorageUuids   //指定添加镜像的镜像服务器UUID列表
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/images',json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            if($rs){
                $result = $this->getPollResult ($rs,$curl);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 删除镜像接口
     * @desc 主要用于删除镜像，只需传入一个必须参数
     * @param string uuid 镜像uuid
     * @param string backupStorageUuids 镜像服务器UUID
     * @return mixed|string
     */
    public function deleteImages ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_del ($this->SERVER . "/zstack/v1/images/" . $uuid . "?deleteMode=Permissive",array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 彻底删除镜像接口
     * @desc 主要用于彻底删除镜像，
     * @param string uuid 镜像uuid
     * @param string backupStorageUuids 镜像服务器UUID
     * @return mixed|string
     */
    public function packImages ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $imageUuid = $this->imageUuid;
            $data = array (
                "expungeImage" => array (
                    "backupStorageUuids" =>
                        $this->backupStorageUuids
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $imageUuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
 * 查询镜像接口
 * @desc 主要用于查询镜像信息,只需传入镜像的uuid
 * @param string uuid 镜像uuid
 * @return mixed|string
 */
    public function selectImages ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $apiAuth = 'admin';
            $uuid = $this->uuid;
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->get ($this->SERVER . '/zstack/v1/images/' . $uuid,$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数表示超时时间，单位为毫秒
            return $rs;
        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 查询镜像列表接口
     * @desc 主要用于查询镜像列表信息
     * @return mixed|string
     */
    public function selectImagesList ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);

            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->get ($this->SERVER . '/zstack/v1/images',$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数表示超时时间，单位为毫秒
            return $rs;
        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 恢复被删除（但未彻底删除）的镜像接口
     * @desc 主要用于恢复删除镜像（不包括彻底删除的镜像），需传递两个必须参数
     * @param string imageuuid 镜像uuid
     * @param string backupStorageUuids 镜像服务器UUID
     * @return mixed|string
     */
    public function recoverImages ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $imageUuid = $this->imageUuid;
            $data = array (
                "recoverImage" => array (
                    "backupStorageUuids" =>
                        $this->backupStorageUuids
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $imageUuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待delete的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 改变镜像启用状态
     * @desc 主要用于更改镜像的启用状态，需要传递两个必须参数，其中参数stateEvent值为enable是启用，disable是停用
     * @param string uuid 镜像uuid
     * @param string stateEvent 状态事件
     * @return mixed|string
     */
    public function changeImagesState ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $data = array (
                'changeImageState' => array (
                    "stateEvent" => $this->stateEvent,  //只有两个参数，enable表示开启，disable表示不启用
                )
            );
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                 $result = $this->getPollResult ($rs,$curls);
             }
            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 更新镜像的信息接口
     * @desc 主要用于更新镜像的信息，需要传递一个必须参数，可选参数任选一个或多个
     * @param string uuid 镜像uuid
     * @param string platform 镜像的系统平台
     * @return mixed|string
     */
    public function updateImages ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                "updateImage" => array (
                    "platform" => $this->platform,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 刷新镜像大小信息接口
     * @desc 主要用于刷新镜像大小信息，需要传递一个必须参数，可选参数任选一个或多个
     * @param string uuid 镜像uuid
     * @return mixed|string
     */
    public function syncImageSize ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                'syncImageSize' => (object)array (),
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }


    /**
     * 获取镜像服务器候选接口
     * @desc 主要用于获取创建镜像的镜像服务器候选，可以不用传递参数
     * @param string volumeUuid 云盘uuid
     * @return mixed|string
     */
    public function getImagesServerCandidate ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $volumeUuid = $this->volumeUuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . '/zstack/v1/images/volumes/' . $volumeUuid . '/candidate-backup-storage',$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 从根云盘创建根云盘镜像接口
     * @desc 主要用于从根云盘创建根云盘镜像，需要传入两个必须参数
     * @param string name 根云盘镜像
     * @param string rootVolumeUuid 根云盘UUID
     * @param string system 是否系统根云盘镜像
     * @param string platform 根云盘镜像对应的系统平台
     * @param string backupStorageUuids 镜像服务器UUID列表
     * @return mixed|string
     */
    public function createRootVolumeFromImages ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $rootVolumeUuid = $this->rootVolumeUuid;
            $data = array (
                'params' => array (
                    "name" => $this->name,
                    "backupStorageUuids" => $this->backupStorageUuids,  //被添加镜像的URL地址
                    "platform" => $this->platform,  //镜像类型，分为RootVolumeTemplate，ISO，DataVolumeTemplate
                    "system" => $this->system,   //是否系统镜像
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/images/root-volume-templates/from/volumes/' . $rootVolumeUuid,json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            if($rs){
                $result = $this->getPollResult ($rs,$curl);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 创建根云盘镜像接口
     * @desc 主要用于从根云盘创建根云盘镜像，需要传入三个必须参数
     * @param string name 镜像名称
     * @param string snapshotUuid 快照UUID
     * @param string system 是否系统根云盘镜像
     * @param string backupStorageUuids 镜像服务器UUID列表
     * @return mixed|string
     */
    public function createRootVolumeImages ()
    {
        try {
            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $snapshotUuid = $this->snapshotUuid;
            $data = array (
                'params' => array (
                    "name" => $this->name,
                    "backupStorageUuids" => $this->backupStorageUuids,  //镜像服务器UUID列表
                    "system" => $this->system,   //是否系统镜像
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/images/root-volume-templates/from/volume-snapshots/' . $snapshotUuid,json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            if($rs){
                $result = $this->getPollResult ($rs,$curl);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 从云盘创建数据云盘镜像
     * @desc 主要用于从云盘创建数据云盘镜像，需要传入三个必须参数
     * @param string name 镜像名称
     * @param string snapshotUuid 快照UUID
     * @param string system 是否系统根云盘镜像
     * @param string backupStorageUuids 镜像服务器UUID列表
     * @return mixed|string
     */
    public function createDataVolumeImages ()
    {
        try {

            // 实例化时也可指定失败重试次数，这里是2次，即最多会进行3次请求
            $curl = new \PhalApi\CUrl(2);
            $volumeUuid = $this->volumeUuid;
            $data = array (
                'params' => array (
                    "name" => $this->name,
                    "backupStorageUuids" => $this->backupStorageUuids,  //镜像服务器UUID列表
                )
            );
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存里的会话uuid

            $curl->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curl->post ($this->SERVER . '/zstack/v1/images/data-volume-templates/from/volumes/' . $volumeUuid,json_encode ($data),$this->outTime),true);
            //发送请求，获取轮询地址，第二个参数为待POST的数据；第三个参数表示超时时间，单位为毫秒
            if($rs){
                $result = $this->getPollResult ($rs,$curl);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 获取镜像Qga
     * @desc 主要用于获取镜像Qga，只需传递一个必要参数
     * @param string volumeUuid 镜像uuid
     * @return mixed|string
     */
    public function getImageQga ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $curls->setHeader (array ( 'Authorization' => $Oauth ));
            $rs = json_decode ($curls->get ($this->SERVER . '/zstack/v1/images/' . $uuid . '/qga',$this->outTime),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    /**
     * 设置镜像qga接口
     * @desc 主要用于设置镜像qga接口，需要传递一个必须参数，可选参数任选一个或多个
     * @param string uuid 镜像uuid
     * @return mixed|string
     */
    public function setImageQga ()
    {
        try {
            $curls = new \PhalApi\CUrl(2);
            $curl = new Curl();
            //实例化公共curl类
            $uuid = $this->uuid;
            $apiAuth = 'admin';
            $Oauth = \PhalApi\DI ()->cache->get ('Login:' . $apiAuth);
            //获取缓存中的会话uuid
            $data = array (
                'setImageQga' => array (
                    "enable" => $this->enable,
                )
            );

            $rs = json_decode ($curl->curl_put ($this->SERVER . "/zstack/v1/images/" . $uuid . "/actions",json_encode ($data),array ( 'Authorization:' . $Oauth )),true);
            // 发送请求，获取轮询地址，第二个参数为待put的数据；第三个参数表示header头信息
            if($rs){
                $result = $this->getPollResult ($rs,$curls);
            }

            return $result;

        } catch ( \PhalApi\Exception\InternalServerErrorException $ex ) {
            return $ex->getMessage ();
        }
    }

    private function getPollResult ($data_url,$urls){
        $re_arr=parse_url ($data_url->location);
        $res=$this->SERVER.$re_arr["path"];
        //获取轮询地址
        return $urls->get($res,$this->outTime);
        //再次请求轮询地址获取结果
    }
}