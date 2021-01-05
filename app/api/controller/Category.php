<?php


namespace app\api\controller;
use app\common\business\Category as CategoryBus;
use app\common\lib\Tree;
use think\Exception;

class Category extends ApiBase
{
    /**
     * 首页无限分类
     * @return \think\response\Json
     */
    public function index(){
        $categoryBus = new CategoryBus();
        try {
            $categorys = $categoryBus->getAllCategorys();
        }catch (Exception $e){
            throw new Exception("系统异常");
        }
        $categorysTreeObj = new Tree();
        $categorysTreeInfo = $categorysTreeObj::getTree($categorys);
        $categorysTreeInfo = $categorysTreeObj::sliceTree($categorysTreeInfo);
        if (empty($categorysTreeInfo)){
            $categorysTreeInfo = null;
        }
        return show(config("status.success"),'OK',$categorysTreeInfo);
    }
}