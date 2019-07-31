<?php


namespace App\Domain;


use App\Model\AdminMenu;

class MenuDomain
{
    private  $model;

     public function __construct(){
         $this->model=new AdminMenu();
    }
    public function index(){
       return  $this->model->index();
    }
    public function addMenu($params){

        return $this->model->addMenu($params);
    }
    public function updateMenu($id,$data){
         return $this->model->updateMenu($id,$data);
    }
    public function delMenu($id){
        return $this->model->delMenu($id);
    }
    public function getName($url){
        return $this->model->getMenuName($url);
    }
    /**
     * 把返回的数据集转换成Tree
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param string $root
     * @return array
     */
    function listToTree($list, $pk='id', $pid = 'fid', $child = '_child', $root = '0') {
        $tree = array();
        if(is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    function formatTree($list, $lv = 0, $title = 'name'){
        $formatTree = array();
        foreach($list as $key => $val){
            $title_prefix = '';
            for( $i=0;$i<$lv;$i++ ){
                $title_prefix .= "|---";
            }
            $val['lv'] = $lv;
            $val['namePrefix'] = $lv == 0 ? '' : $title_prefix;
            $val['showName'] = $lv == 0 ? $val[$title] : $title_prefix.$val[$title];
            if(!array_key_exists('_child', $val)){
                array_push($formatTree, $val);
            }else{
                $child = $val['_child'];
                unset($val['_child']);
                array_push($formatTree, $val);
                $middle = $this->formatTree($child, $lv+1, $title); //进行下一层递归
                $formatTree = array_merge($formatTree, $middle);
            }
        }
        return $formatTree;
    }
}
