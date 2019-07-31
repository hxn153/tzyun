<?php
/**文章模块*/

namespace Website\Api;


use PhalApi\Api;
use Website\Domain\Article as DomainArticle;

/**
 * 文章模块
 * Class Article
 * @package Website\Api
 */

class Article extends Api
{

    public function getRules() {
        return array(
            'getArticlesDetils' => array(
                'id' => array('name' => 'id', 'require' => true,'desc' => '文章id'),
            ),
            'getArticleColumn' => array(
                'id' => array('name' => 'id', 'require' => false,'defualt'=>2,'desc' => '栏目id'),
                'pagesize' =>array('name' => 'pagesize', 'require' => false,'defualt'=>10,'desc' => "栏目文章数量"),
            ),
            'getRecommendArticle' => array(
                'count' => array('name' => 'count', 'require' => true,'desc' => '文章条数'),
            ),
        );
    }


    /**
     * 获取文章列表
     * @desc 获取全部文章列表
     * @return mixed
     */
    public function getArticle(){
        $article=new DomainArticle();
        return $article->getArticle();
    }


    /**
     * 获取文章详情
     * @desc 获取指定文章详情
     * @return array
     */
    public function getArticlesDetils(){
        $article=new DomainArticle();
        //$art_id=$this->id;
        $request = \PhalApi\DI()->request;
        $id=$request->get("id");
        return $article->getArticleDetils($id);
    }

    /**
     * 获取文章栏目
     * @desc 获取首页推荐文章栏目
     * @return array
     */
    public function getArticleColumn(){
        $article=new DomainArticle();
        $art_arr=array(
            'col_id' =>$this->id,
            'pagesize' =>$this->pagesize,
        );
        return $article->getArticleColumn($art_arr);
    }

    /**
     * 获取最新文章动态
     * @desc 获取首页文章最新动态
     * @return array
     */
    public function getNewArticle(){
        $article=new DomainArticle();
        return $article->getNewArticle();
    }

    /**
     * 获取推荐文章
     * @desc 文章详情右侧的文章推荐
     * @return array
     */
    public function getRecommendArticle(){
        $article=new DomainArticle();
        $con=$this->count;
        return $article->getRecommendArticle($con);
    }


}