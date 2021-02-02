<?php


namespace app\common\business;

use app\common\lib\Arr;
use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBus;
use think\Exception;
use think\facade\Cache;

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
    public function getLists($data, $num, $where)
    {
        $likeKeys = [];
        if (!empty($data)) {
            $likeKeys = array_keys($data);
        }
        try {
            $goods = $this->model->getLists($likeKeys, $data, $num, $where);
            $result = $goods->toArray();
        } catch (Exception $e) {
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
    public function insertData($data)
    {
//        开启事务
        $this->model->startTrans();
        try {
//        新增商品基本信息
            $goods_id = $this->save($data);
            if (!$goods_id) {
                return false;
            }
//        goods_specs_type为1:统一规格，2:多规格
//        todo 统一规格逻辑未处理
            if ($data['goods_specs_type'] == 1) {

            } elseif ($data['goods_specs_type'] == 2) {
                $data['goods_id'] = $goods_id;
//            新增商品sku
                $goodsSkuBus = new GoodsSkuBus();
                $res = $goodsSkuBus->saveAll($data);
                if (!empty($res)) {
//                计算所有规格属性总库存
                    $stock = array_sum(array_column($res, 'stock'));
                    $goodsUpdateSave = [
                        "stock" => $stock,
                        "price" => $res[0]["price"],
                        "cost_price" => $res[0]["cost_price"],
                        "sku_id" => $res[0]["id"],
                    ];
//                更新商品表信息
                    $result = $this->model->updateById($goods_id, $goodsUpdateSave);
                    if (!$result) {
                        throw new Exception("商品信息表更新失败");
                    }
                } else {
                    throw new Exception("商品sku信息表更新失败");
                }
            }
//            事务提交
            $this->model->commit();
            return $result;
        } catch (Exception $e) {
//            事务回滚
            $this->model->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * 获取首页轮播图
     * @return array
     */
    public function getRotationChart()
    {
        $field = "sku_id as id,title,big_image as image";
        try {
            $result = $this->model->getRotationChart($field);
        } catch (Exception $e) {
            return [];
        }
        return $result->toArray();
    }

    /**
     * 根据分类ID获取商品
     * @param $categoryIds
     * @return array
     */
    public function categoryGoodsRecommend($categoryIds)
    {
        if (!$categoryIds) {
            return [];
        }
        $result = [];
//        获取分类栏目
        $categoryBus = new Category();
        foreach ($categoryIds as $key => $categoryId) {
//            根据id获取顶级分类信息
            $result[$key]['categorys'] = $categoryBus->getGoodsRecommendById($categoryId);
//            根据PID获取二级分类信息
            $result[$key]['categorys']['list'] = $categoryBus->getGoodsRecommendByPid($categoryId);
        }

//        获取分类下的商品
        foreach ($categoryIds as $key => $categoryId) {
            $result[$key]['goods'] = $this->getGoodsByCategoryId($categoryId);
        }
        return $result;
    }

    /**
     * 根据分类ID获取商品
     * @param $categoryId
     * @return array
     */
    public function getGoodsByCategoryId($categoryId)
    {
        $field = "sku_id as id,title,price,recommend_image as image";
        try {
            $result = $this->model->getGoodsByCategoryId($categoryId, $field);
        } catch (Exception $e) {
            return [];
        }
        return $result->toArray();
    }

    /**
     * 按分类查询商品数据/按关键字搜索商品
     * @param $categoryId
     * @param $pageSize
     * @param $order
     * @return array
     */
    public function getGoodsLists($categoryId, $pageSize, $order, $keywords)
    {
        $field = "sku_id as id,title,price,recommend_image as image,sales_count";
        try {
            $res = $this->model->getGoodsLists($categoryId, $field, $pageSize, $order, $keywords)->toArray();
            $result = [];
            $result['total_page_num'] = isset($res['per_page']) ? $res['per_page'] : 0;
            $result['count'] = isset($res['total']) ? $res['total'] : 0;
            $result['page'] = isset($res['current_page']) ? $res['current_page'] : 0;
            $result['page_size'] = isset($pageSize) ? $pageSize : 10;
            $result['list'] = isset($res['data']) ? $res['data'] : [];
        } catch (Exception $e) {
            return [];
        }
        return $result;
    }

    /**
     * 按skuId查询商品详情数据
     * @param $skuID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsDetailBySkuId($skuID)
    {
        $goodsSkuBus = new GoodsSkuBus();
//        按skuId获取商品详情
        $goodsDetail = $goodsSkuBus->getGoodsDetailBySkuId($skuID);
        if (!$goodsDetail) {
            return [];
        }
        if (!$goodsDetail['goods']) {
            return [];
        }

        $goods = $goodsDetail['goods'];
//        按商品id获取所有sku数据
        $skus = $goodsSkuBus->getSkusByGoodsId($goods['id']);
        if (!$skus) {
            return [];
        }
        $gids = array_column($skus, "id", "specs_value_ids");
//        用当前skuId获取规格属性（返回flag标记：用作高亮显示）
        $flagValue = "";
        foreach ($gids as $k => $value) {
            if ($value == $skuID) {
                $flagValue = $k;
            }
        }

//        获取当前商品的sku
        $specsValueBus = new SpecsValue();
        if ($goods['goods_specs_type'] == 1) {
            $sku = [];
        } else {
            $sku = $specsValueBus->getSpecsValueById($gids, $flagValue);
        }

        $result['title'] = $goods['title'];
        $result['price'] = $goodsDetail['price'];
        $result['cost_price'] = $goodsDetail['cost_price'];
        $result['sales_count'] = $goods['sales_count'];
        $result['stock'] = $goodsDetail['stock'];
        $result['gids'] = $gids;
        $result['image'] = explode(",", $goods['carousel_image']);
        $result['sku'] = $sku;
        $result['detail'] = [
            "d1" => [
                "商品编号" => $goodsDetail['id'],
                "上架时间" => $goods['create_time'],
                "商品库存" => $goodsDetail['stock'],
            ],
//            "d2" => preg_replace('/(<img .*?src=")(.*?)/','$1'.'http://localhost'.'$2',$goods['description'])
            "d2" => $goods['description']
        ];
//        使用redis记录用户浏览商品详情PV统计
        Cache::inc(config("redis.mall_pv") . $goods['id']);
        return $result;
    }

    /**
     * 更新商品库存
     * @param $sumStock
     * @return bool|\think\Collection
     * @throws \Exception
     */
    public function updateStock($sumStock)
    {
        try {
            $result = $this->model->saveAll($sumStock);
        } catch (Exception $e) {
            return false;
        }
        return $result;
    }

    /**
     * 更改秒杀商品状态
     * @param $data
     */
    public function changeStatus($data)
    {
        $goodsInfo = $this->getById($data['id'])->toArray();
        if (empty($goodsInfo)) {
            throw new Exception('不存在当前商品信息');
        }
        if ($goodsInfo['status'] == $data['status']) {
            throw new Exception('请勿多次修改相同的商品状态');
        }

        //按当前商品id查出所有当前商品sku数据存入redis（秒杀商品）
        $goodsSkuBus = new GoodsSkuBus();
        $goodsSkuInfo = $goodsSkuBus->getSkusByGoodsId($data['id']);
        if (!$goodsSkuInfo) {
            throw new Exception('不存在当前商品sku信息');
        }
        $skuIds = array_column($goodsSkuInfo,'id');
        //如果是关闭秒杀商品则清空当前redis存储的商品信息
        if ($data['status'] == config("status.error") && $skuIds){
            foreach ($skuIds as $skuId){
                Cache::hDel("goods-spike",$skuId);
            }
        }else{
            //开启一个秒杀商品需判断当前存不存在秒杀商品（如果存在则提示关闭）
            $isSpike = $this->model->isSpikeGoods($data)->toArray();
            if ($isSpike){
                throw new Exception('当前已有秒杀商品,请先将其关闭');
            }
            //如果开启一个秒杀商品则将当前秒杀商品的所有sku信息存入redis
            foreach ($goodsSkuInfo as $goodsSku) {
                $redisData = [
                    "title" => $goodsInfo['title'],
                    "image" => $goodsInfo['recommend_image'],
                    "price" => $goodsSku['price'],
                    "stock" => $goodsSku['stock'],
//                用户地址应是用户下单传的（伪造测试）
                    "address_id" => "1",
                    "goods_id" => $goodsSku['goods_id'],
                    "specs_value_ids" => $goodsSku['specs_value_ids'],
                    "sku_id" => $goodsSku['id'],
                    "start_time" => $goodsInfo['spike_start_time'],
                    "end_time" => $goodsInfo['spike_end_time'],
                ];
                Cache::hSet("goods-spike",$goodsSku['id'],json_encode($redisData));
            }
        }

        try {
            $result = $this->model->changeStatus($data);
        } catch (Exception $e) {
            throw new Exception('系统错误');
        }

        return $result;
    }

    /**
     * 根据id获取数据
     * @param $id
     */
    public function getById($id)
    {
        $categoryInfo = $this->model->find($id);
        if (!$categoryInfo) {
            return [];
        }
        return $categoryInfo;
    }
}