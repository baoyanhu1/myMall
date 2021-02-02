<?php


namespace app\admin\controller;


use app\common\lib\Oss;
use app\common\lib\Show;
use think\Exception;
use think\facade\View;
use app\common\business\Goods as GoodsBus;
use app\admin\validate\Goods as GoodsVil;

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
        $where = [
            'is_spike' => config("status.mysql.is_no_spike")
        ];
        $goods = $goodsBus->getLists($data,3,$where);
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
     * 渲染秒杀商品列表页
     * @return string
     */
    public function spike(){
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
        $where = [
            'is_spike' => config("status.mysql.is_spike")
        ];
        $goods = $goodsBus->getLists($data,3,$where);
        return View::fetch("",[
            'goods'=>$goods
        ]);
    }

    /**
     * 修改商品状态
     * @return \think\response\Json
     */
    public function changeStatus(){
        $id = input("param.id","","intval");
        $status = input("param.status","","intval");
        $data = [
            'id' => $id,
            'status' => $status
        ];
//        验证参数
        $goodsVil = new GoodsVil();
        $check = $goodsVil->scene('changeStatus')->check($data);
        if (!$check){
            return show(config("status.error"),$goodsVil->getError());
        }

        // 修改状态
        try {
            $goodsBus = new GoodsBus();
            $result = $goodsBus->changeStatus($data);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }
        if (!$result){
            return show(config("status.error"),"状态修改失败");
        }
        return show(config("status.success"),"状态修改成功");
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
        $data['is_spike'] = $data['goods_spike'];
//        如果是秒杀商品（处理秒杀开始/结束时间）
        if ($data['is_spike'] == config("status.mysql.is_spike")){
            if (empty($data['spike_time'])){
                return show(config("status.error"),"秒杀场景必选秒杀时间");
            }
            $spike_time = explode("~",trim($data['spike_time']));
            $spike_start_time = $spike_time[0];
            $spike_end_time = $spike_time[1];
            if (!$spike_start_time){
                return show(config("status.error"),"秒杀开始时间必选");
            }
            if (!$spike_end_time){
                return show(config("status.error"),"秒杀结束时间必选");
            }
            $data['spike_start_time'] = strtotime(trim($spike_start_time));
            $data['spike_end_time'] = strtotime(trim($spike_end_time));
            $data['status'] = config("status.error");
        }

        try {
//            新增商品业务逻辑
            $goodsBus = new GoodsBus();
            $result = $goodsBus->insertData($data);
        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        return show(config("status.success"),"商品新增成功");

    }

    /**
     * 删除图片
     * @return \think\response\Json
     */
    public function deleteIamge(){
        $image = input("param.src","","trim");
        $data = [
            "src" => $image
        ];
        $goodsVil = new GoodsVil();
        $check = $goodsVil->scene("deleteImage")->check($data);
        if (!$check){
            return Show::error([],$goodsVil->getError());
        }
        $ossObj = new Oss();
        try {
            $image = explode("/",$image);
            $image = end($image);
            $result = $ossObj->delete($image);
            if ($result['status'] == 0){
                return Show::error([],"删除失败");
            }
        }catch (Exception $e){
            return Show::error([],"删除失败");
        }
        return Show::success($result);
    }
}