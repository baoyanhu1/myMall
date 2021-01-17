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

    /**
     * 根据商品分类获取前端首页商品
     * @return \think\response\Json
     */
    public function categoryGoodsRecommend(){
//        todo 默认获取两个分类下的商品 后期优化
        $categoryIds = [
            20,41
        ];
        $goodsBus = new GoodsBus();
        $result = $goodsBus->categoryGoodsRecommend($categoryIds);
        return Show::success($result);
    }
}