<?php


namespace app\api\controller;


use app\common\business\Goods as GoodsBus;
use app\common\lib\Show;

class Index extends ApiBase
{
    /**
     * 获取首页轮播图
     * @return \think\response\Json
     */
    public function getRotationChart(){
        $goodsBus = new GoodsBus();
        $result = $goodsBus->getRotationChart();
        return Show::success($result);
    }
}