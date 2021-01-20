<?php


namespace app\common\model\mysql;


class Area extends BaseModel
{
    /**
     * 获取用户收货地址省市区
     * @param $areaIds
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getArea($areaIds){
        $result = $this->whereIn("id",$areaIds)->select();
        return $result;
    }
}