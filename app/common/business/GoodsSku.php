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

    /**
     * 按skuId查询商品
     * @param $skuId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsDetailBySkuId($skuId){
        try {
            $goodsDetail = $this->model->with("goods")->find($skuId);
        }catch (Exception $e){
            return [];
        }

        if (!$goodsDetail){
            return [];
        }
        $result = $goodsDetail->toArray();
        if ($result['status'] != config("status.mysql.table_normal")){
            return [];
        }
        return $result;
    }

    /**
     * 按商品ID获取所有sku数据
     * @param $goodsId
     * @return array
     */
    public function getSkusByGoodsId($goodsId){
        try {
            $result = $this->model->getSkusByGoodsId($goodsId);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }

    /**
     * 根据skuids获取所有sku数据
     * @param $ids
     * @return array
     */
    public function getNormalInIds($ids){
        try {
            $result = $this->model->getNormalInIds($ids);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }
}