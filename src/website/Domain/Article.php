<?php
/**
 * Created by PhpStorm.
 * User: lxd
 * Date: 2019/6/14
 * Time: 13:53
 */

namespace Website\Domain;
use Website\Model\Article as ModelArticle;


class Article
{
    public function getArticle(){
        $article=new ModelArticle();
        $art=$article->getArticle();
        return $art;
    }

    public function getArticleDetils($art_id){
        $article=new ModelArticle();
        $art_det=$article->getArticleDetils($art_id);

        //$art_det["times"]=date("Y.m.d",$art_det["add_time"]);
        return $art_det;
    }

    public function getArticleColumn($art_arr){
        $article=new ModelArticle();
        $art_coluomn=$article->getArticleColumn($art_arr);

        return $art_coluomn;
    }

    public function getNewArticle(){
        $article=new ModelArticle();
        $art=$article->getNewArticle();
        return $art;
    }

    public function getRecommendArticle($con){
        $article=new ModelArticle();
        $art=$article->getRecommendArticle($con);
        return $art;
    }
}