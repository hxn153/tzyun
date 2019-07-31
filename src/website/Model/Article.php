<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/14
 * Time: 13:51
 */

namespace Website\Model;


use PhalApi\Model\NotORMModel;

class Article extends NotORMModel
{
    public function getArticle(){
        return $this->getORM()->select("id,cid,title,thumb,add_time,describes")->fetchAll();
    }

    public function getArticleDetils($art_id){
        $sql_art="SELECT a.cname,b.* FROM wpg_article_class a LEFT JOIN wpg_article b ON a.id=b.cid WHERE b.id=?";
        $sql_pre=$this->getORM()->select("id,title")->where("id < ?",$art_id)->
        order("id DESC")->limit(0,1)->fetchAll();
        $sql_next=$this->getORM()->select("id,title")->where("id > ?",$art_id)->limit(0,1)->fetchAll();
        $arts=$this->getORM()->queryAll($sql_art,array($art_id));
        $arts["pre_art"]=$sql_pre;
        $arts["next_art"]=$sql_next;
        return $arts;


    }

    public function getArticleColumn($art_arr){
        $col_id=$art_arr["col_id"];
        $pages=intval($art_arr["pagesize"]);
        $sql="SELECT a.cname,b.title,b.add_time FROM article_class a LEFT JOIN article b ON a.id=b.cid WHERE
              a.id=? AND b.rid=1 ORDER BY b.pid DESC  LIMIT 0,? ";
        return $this->getORM()->queryAll($sql,array($col_id,$pages));
    }

    public function getNewArticle(){
        return $this->getORM()->select("id,cid,title,thumb,add_time,describes")->ORDER("add_time DESC")->limit(4)->fetchAll();
    }

    public function getRecommendArticle($con){
        return $this->getORM()->select("id,cid,title,thumb")->where("rid=1")->limit($con)->fetchAll();
    }
}