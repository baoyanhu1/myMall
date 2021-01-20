<?php


namespace app\common\business;
use app\common\model\mysql\Area as AreaModel;
use think\Exception;

class Area extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new AreaModel();
    }

    /**
     * 获取用户收货地址省市区
     * @param $areaIds
     * @return array
     */
    public function getArea($areaIds){
        try {
            $result = $this->model->getArea($areaIds);
        }catch (Exception $e){
            return [];
        }
        if (!$result){
            return [];
        }
        return $result->toArray();
    }
}