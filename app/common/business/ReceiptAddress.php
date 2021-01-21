<?php


namespace app\common\business;
use app\common\model\mysql\ReceiptAddress as ReceiptAddressModel;
use think\Exception;

class ReceiptAddress extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new ReceiptAddressModel();
    }

    /**
     * 根据id获取用户收货地址
     * @param $addressId
     * @return array|bool
     */
    public function getReceiptAddress($addressId){
        if (!$addressId){
            return false;
        }
        $condition = [
            "id" => $addressId
        ];
        try {
            $result = $this->model->getByCondition($condition);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }
}