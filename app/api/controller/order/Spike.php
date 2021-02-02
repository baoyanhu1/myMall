<?php


namespace app\api\controller\order;


use app\api\controller\AuthBase;
use app\common\business\Order as OrderBus;
use app\common\lib\Show;
use think\Exception;

class Spike extends AuthBase
{
    /**
     * 根据skuId获取redis内存储的商品信息
     * @return \think\response\Json
     */
    public function index(){
        $id = input("param.id","","intval");
        if (!$id){
            return Show::error([],"未选择要秒杀的商品");
        }
        $orderBus = new OrderBus();
        try {
            $spikeGoods = $orderBus->getSpikeOrderBySkuId($id);
        }catch (Exception $e){
            return Show::error([],$e->getMessage());
        }
        return Show::success($spikeGoods);
    }
}