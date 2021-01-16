<?php


namespace app\common\business;
use app\common\lib\Arr;
use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBus;
use think\Exception;

class Goods extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new GoodsModel();
    }

    /**
     * 分页查询商品
     * @param $num
     * @return Arr|array
     */
    public function getLists($data,$num){
        $likeKeys = [];
        if (!empty($data)){
            $likeKeys = array_keys($data);
        }
        try {
            $goods = $this->model->getLists($likeKeys,$data,$num);
            $result = $goods->toArray();
        }catch (Exception $e){
            $result = new Arr();
        }

       return $result;
    }

    /**
     * 新增商品
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function insertData($data){
//        开启事务
        $this->model->startTrans();
        try {
//        新增商品基本信息
            $goods_id = $this->save($data);
            if (!$goods_id){
                return false;
            }
//        goods_specs_type为1:统一规格，2:多规格
//        todo 统一规格逻辑未处理
            if ($data['goods_specs_type'] == 1){

            }elseif ($data['goods_specs_type'] == 2){
                $data['goods_id'] = $goods_id;
//            新增商品sku
                $goodsSkuBus = new GoodsSkuBus();
                $res = $goodsSkuBus->saveAll($data);
                if (!empty($res)){
//                计算所有规格属性总库存
                    $stock = array_sum(array_column($res,'stock'));
                    $goodsUpdateSave = [
                        "stock" => $stock,
                        "price" => $res[0]["price"],
                        "cost_price" => $res[0]["cost_price"],
                        "sku_id" => $res[0]["id"],
                    ];
//                更新商品表信息
                    $result = $this->model->updateById($goods_id,$goodsUpdateSave);
                    if (!$result){
                        throw new Exception("商品信息表更新失败");
                    }
                }else{
                    throw new Exception("商品sku信息表更新失败");
                }
            }
//            事务提交
            $this->model->commit();
            return $result;
        }catch (Exception $e){
//            事务回滚
            $this->model->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取首页轮播图
     * @return array
     */
    public function getRotationChart(){
        $field = "sku_id as id,title,big_image as image";
        try {
            $result = $this->model->getRotationChart($field);
        }catch (Exception $e){
            return [];
        }
        return $result->toArray();
    }
}