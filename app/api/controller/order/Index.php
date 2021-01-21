<?php


namespace app\api\controller\order;


use app\api\controller\AuthBase;
use app\api\validate\Order;
use app\common\lib\Show;
use app\common\business\Order as OrderBus;
use think\Exception;

class Index extends AuthBase
{
    /**
     * 创建订单
     * @return \think\response\Json
     */
    public function save(){
        if (!$this->request->isPost()){
            return Show::error([],"请求方式错误");
        }
        $ids = input("param.ids","","trim");
        $address_id = input("param.address_id","","intval");
        $data = [
            "ids" => $ids,
            "address_id" => $address_id,
            "user_id" => $this->user_id
        ];
        $orderVil = new Order();
        $check = $orderVil->scene("save")->check($data);
        if (!$check){
            return Show::error([],$orderVil->getError());
        }
        try {
            $orderBus = new OrderBus();
            $result = $orderBus->saveOrder($data);
        }catch (Exception $e){
            return Show::error([],$e->getMessage());
        }
        if (!$result){
            Show::error([],"提交订单失败,请稍后重试");
        }
        return Show::success($result);
    }

    /**
     * 获取订单详细信息
     * @return \think\response\Json
     */
    public function read(){
        $order_id = input("param.id","","trim");
        $data = [
            "id" => $order_id
        ];
        $orderVil = new Order();
        $check = $orderVil->scene("read")->check($data);
        if (!$check){
            return Show::error([],$orderVil->getError());
        }
        $orderData = [
            "id" => $order_id,
            "user_id" => $this->user_id
        ];
        $orderBus = new OrderBus();
        $result = $orderBus->orderDetail($orderData);
        if (!$result){
            return Show::error([],$orderVil->getError());
        }
        return Show::success($result);
    }
}