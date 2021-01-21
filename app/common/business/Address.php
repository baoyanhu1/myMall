<?php


namespace app\common\business;

use app\common\model\mysql\ReceiptAddress;
use think\Exception;

class Address extends BusBase
{
    public $model;

    public function __construct()
    {
        $this->model = new ReceiptAddress();
    }

    /**
     * 获取用户收货地址
     * @param $userId
     * @return array
     */
    public function getReceiptAddress($userId)
    {
        try {
//            获取用户收货地址
            $result = $this->model->getReceiptAddress($userId);
        } catch (Exception $e) {
            return [];
        }
        $result = $result->toArray();

        if ($result) {
            foreach ($result as $k => $value) {
                $areaIds = $value['area'];
                try {
//                获取收货省市区地址
                    $areas = $this->getArea($areaIds);
                } catch (Exception $e) {
                    return [];
                }
                $str = "";
//                循环组装数据并拼接省市区
                foreach ($areas as $kk => $area) {
                    $res[$k] = [
                        "id" => $result[$k]["id"],
                        "consignee_info" => $str .= $area['name'],
                        "is_default" => $result[$k]['is_default']
                    ];
                }
//                将详细地址拼接到省市区后
                $res[$k]['consignee_info'] .= $result[$k]['detail_address'] . "  " . $result[$k]['receiver_name'] . "  " . $result[$k]['phone'];
            }

        } else {
            return [];
        }
        return $res;
    }

    /**
     * 获取用户收货地址省市区
     * @param $areaIds
     * @return array
     */
    public function getArea($areaIds)
    {
        $areaIds = explode(",", $areaIds);
        $areaBus = new Area();
        $detailArea = $areaBus->getArea($areaIds);
        return $detailArea;
    }

    /**
     * 根据areaIds返回省市区
     * @param $detailArea
     * @return string[]
     */
    public function getStrArea($detailArea)
    {
        $str = "";
//                循环组装数据并拼接省市区
        foreach ($detailArea as $kk => $area) {
            $res = [
                "consignee_info" => $str .= $area['name'],
            ];
        }
        return $res;
    }
}