<?php


namespace app\common\business;
use app\common\model\mysql\OrderGoods as OrderGoodsModel;
use think\Exception;

class OrderGoods extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new OrderGoodsModel();
    }

    /**
     * 批量增加数据到订单附表
     * @param $data
     * @return bool|\think\Collection
     * @throws \Exception
     */
    public function saveAll($data){
        try {
            $result = $this->model->saveAll($data);
        }catch (Exception $e){
            return false;
        }
        return $result;
    }

    /**
     * 根据订单ID获取数据
     * @param $orderId
     * @return array|bool
     */
    public function getOrderGoodsByOrderId($orderId){
        if (!$orderId){
            return false;
        }
        $condition = [
            "order_id" => $orderId
        ];
        try {
            $result = $this->model->getByCondition($condition);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }
}