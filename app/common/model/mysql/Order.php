<?php


namespace app\common\model\mysql;


class Order extends BaseModel
{
    /**
     * 根据订单ID获取订单信息
     * @param $orderId
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getOrderStatus($orderId){
        $where = [
            "order_id" => $orderId,
        ];
        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 根据订单ID更新订单状态
     * @param $orderId
     * @param $orderStatus
     * @return bool
     */
    public function updateOrderStatus($orderId,$orderStatus){
        $save_data = [
            'status' => $orderStatus,
            'update_time' => time()
        ];
        $result = $this->where('order_id',$orderId)->save($save_data);
        return $result;
    }
}