<?php


namespace app\common\business;
use app\common\model\mysql\GoodsSku as GoodsSkuModel;
use think\Exception;

class GoodsSku extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new GoodsSkuModel();
    }

    /**
     * 新增商品sku信息
     * @param $data
     * @return array|bool
     * @throws \Exception
     */
    public function saveAll($data){
        if (!$data['skus']){
            return false;
        }

//        循环每一条sku数据新增入库
        foreach ($data['skus'] as $value){
            $result[] = [
              'goods_id' => $data['goods_id'],
                'specs_value_ids' => $value['propvalnames']['propvalids'],
                'price' => $value['propvalnames']['skuSellPrice'],
                'cost_price' => $value['propvalnames']['skuMarketPrice'],
                'stock' => $value['propvalnames']['skuStock'],
            ];
        }
        try {
            $result = $this->model->saveAll($result);
            return $result->toArray();
        }catch (Exception $e){
            return false;
        }

    }
}