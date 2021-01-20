<?php


namespace app\api\controller;


use app\common\lib\Show;
use app\api\validate\Cart as CartVil;
use app\common\business\Cart as CartBus;
use think\Exception;

class Cart extends AuthBase
{
    public function add(){
        if (!$this->request->isPost()){
            return Show::error([],"请求方式错误");
        }
//        接收参数
        $id = input("param.id","","intval");
        $num = input("param.num","","intval");
        $data = [
            "id" => $id,
            "num" => $num
        ];
        $cartVil = new CartVil();
//        验证参数
        $check = $cartVil->scene("add")->check($data);
        if (!$check){
            return Show::error([],$cartVil->getError());
        }
        $cartBus = new CartBus();
//        购物车数据存入Redis
        $result = $cartBus->insertRedis($this->user_id,$id,$num);
        if ($result === FALSE){
            return Show::error([],"加入购物车失败");
        }
        return Show::success($result);
    }

    /**
     * 获取购物车列表
     * @return \think\response\Json
     */
    public function Lists(){
        $ids = input("param.id","","trim");
        $cartBus = new CartBus();
        $result = $cartBus->Lists($this->user_id,$ids);
        if (!$result){
            return Show::error();
        }
        return Show::success($result);
    }

    /**
     * 删除购物车商品
     * @return \think\response\Json
     */
    public function delete(){
        $id = input("param.id","","intval");
        $data = [
            "id" => $id
        ];
        $cartVil = new CartVil();
//        验证参数
        $check = $cartVil->scene("del")->check($data);
        if (!$check){
            return Show::error([],$cartVil->getError());
        }
        $cartBus = new CartBus();
        $result = $cartBus->delete($this->user_id,$id);
        if ($result === FALSE){
            return Show::error([],"删除失败");
        }
        return Show::success();
    }

    /**
     * 更新购物车商品数量
     * @return \think\response\Json
     */
    public function update(){
        $id = input("param.id","","intval");
        $num = input("param.num","","intval");
        $data = [
            "id" => $id,
            "num" => $num
        ];
        $cartVil = new CartVil();
//        验证参数
        $check = $cartVil->scene("update")->check($data);
        if (!$check){
            return Show::error([],$cartVil->getError());
        }
        try {
            $cartBus = new CartBus();
            $result = $cartBus->update($this->user_id,$id,$num);
        }catch (Exception $e){
            return Show::error([],$e->getMessage());
        }
        if (!$result){
            return Show::error([],"更新失败");
        }
        return Show::success();
    }
}