<?php


namespace app\api\controller\mall;


use app\api\controller\AuthBase;
use app\common\business\Cart;
use app\common\lib\Show;

class Init extends AuthBase
{
    /**
     * 初始化用户购物车数量
     * @return \think\response\Json
     */
    public function index(){
        $cartBus = new Cart();
        $count = $cartBus->getCartCount($this->user_id);
        $result = [
            "cart_num" => $count
        ];
        return Show::success($result);
    }

}