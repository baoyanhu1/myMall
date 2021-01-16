<?php


namespace app\admin\controller;


use think\Exception;
use think\facade\View;
use app\common\business\Goods as GoodsBus;

class Goods extends AdminBase
{
    /**
     * 渲染商品列表页
     * @return string
     */
    public function index(){
        $title = input("param.title","","trim");
        $time = input("param.time","","trim");
//        按条件搜索商品
        $data = [];
        if (!empty($title)){
            $data['title'] = $title;
        }
        if (!empty($time)){
            $data['create_time'] = explode("~",$time);
        }
        $goodsBus = new GoodsBus();
        $goods = $goodsBus->getLists($data,3);
        return View::fetch("",[
            'goods'=>$goods
        ]);
    }

    /**
     * 渲染商品添加页
     * @return string
     */
    public function add(){
        return View::fetch();
    }

    /**
     * 新增商品
     * @return \think\response\Json
     */
    public function save(){
        if (!$this->request->isPost()){
            return show(config("status.error"),"请求方式错误");
        }
        $data = input("param.");
//        token验证
        $checkToken = $this->request->checkToken('__token__');
        if (!$checkToken){
            return show(config("status.error"),'token不合法');
        }
//        todo  商品提交数据未验证
        $category_id = explode(',',$data['category_id']);
        $category_id = array_diff($category_id,['undefined']);
        $category_path_id = explode(",",$data['category_id']);
        $data['category_path_id'] = array_diff($category_path_id,['undefined']);
        $data['category_path_id'] = implode(",",$data['category_path_id']);
        $data['category_id'] = end($category_id);
        $data['production_time'] = strtotime($data['production_time']);

        try {
//            新增商品业务逻辑
            $goodsBus = new GoodsBus();
            $result = $goodsBus->insertData($data);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return show(config("status.success"),"商品新增成功");

    }
}