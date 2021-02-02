<?php


namespace app\api\controller\mall;


use app\api\controller\ApiBase;
use app\common\business\Goods as GoodsBus;
use app\common\lib\Show;

class SpikeDetail extends ApiBase
{
    /**
     * 商品详情数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index(){
        $sku_id = input("param.id","","intval");
        if (!$sku_id){
            return Show::error();
        }
        $goodsBus = new GoodsBus();
        $result = $goodsBus->getGoodsDetailBySkuId($sku_id);
        if (!$result){
            return Show::error();
        }
        return Show::success($result);
    }
}