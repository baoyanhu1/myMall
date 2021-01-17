<?php


namespace app\api\controller;
use app\common\business\Category as CategoryBus;
use app\common\lib\Show;
use app\common\lib\Tree;
use think\Exception;
use app\api\validate\Category as CategoryVil;

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

    /**
     * 前端分类搜索
     * @return \think\response\Json
     */
    public function search(){
        $id = input("param.id","","intval");
        $data = [
            "id" => $id
        ];
//        参数验证
        $categoryVil = new CategoryVil();
        $check = $categoryVil->scene("search")->check($data);
        if (!$check){
            return Show::error([],config("status.error"),$categoryVil->getError());
        }
        $categoryBus = new CategoryBus();
        $category = $categoryBus->search($id);
        return Show::success($category);
    }

    /**
     * 根据二级栏目id获取三级栏目
     * @return \think\response\Json
     */
    public function sub(){
        $id = input("param.id","","intval");
        $data = [
            "id" => $id
        ];
//        参数验证
        $categoryVil = new CategoryVil();
        $check = $categoryVil->scene("search")->check($data);
        if (!$check){
            return Show::error([],config("status.error"),$categoryVil->getError());
        }
        $categoryBus = new CategoryBus();
        $result = $categoryBus->sub($id);
        return Show::success($result);
    }
}