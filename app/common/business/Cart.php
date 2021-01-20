<?php


namespace app\common\business;


use app\common\lib\Arr;
use app\common\lib\Key;
use think\Exception;
use think\facade\Cache;

class Cart extends BusBase
{
    /**
     * 购物车数据存入Redis
     * @param $userId
     * @param $id
     * @param $num
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function insertRedis($userId,$id,$num){
        $goodsSkuBus = new GoodsSku();
//        根据skuId获取商品信息
        $goodsSku = $goodsSkuBus->getGoodsDetailBySkuId($id);
        if (!$goodsSku){
            return FALSE;
        }
        $data = [
            "title" => $goodsSku['goods']['title'],
            "image" => $goodsSku['goods']['recommend_image'],
            "num" => $num,
            "goods_id" => $goodsSku['goods']['id'],
            "create_time" => time(),
        ];
        try {
//            先判断Redis购物车内，存不存在当前商品（如果存在则将现在的数量加购物车内已有的数量）
            $cart = Cache::hGet(Key::cartKey($userId),$id);
            if ($cart){
//                将Redis内得到的json字符串转换成数组，json_decode第二个参数默认为false返回对象，true返回数组
                $cart = json_decode($cart,true);
                $data['num'] = $data['num'] + $cart['num'];
            }
//            将购物车数据存入Redis
            $result = Cache::hSet(Key::cartKey($userId),$id,json_encode($data));
        }catch (Exception $e){
            return FALSE;
        }
        return $result;
    }

    /**
     * 获取购物车列表
     * @param $userId
     * @return array
     * @throws Exception
     */
    public function Lists($userId,$ids){
        try {
            if ($ids){
//                购物车选中商品到结算页面的商品数据
                $ids = explode(",",$ids);
                $allCart = Cache::hMget(Key::cartKey($userId),$ids);
            }else{
                //            获取当前用户购物车数据
                $allCart = Cache::hGetAll(Key::cartKey($userId));
            }
        }catch (Exception $e){
            return [];
        }
        $skuIds = array_keys($allCart);
//        获取商品价格
        $goodsSkuBus = new GoodsSku();
        $skus = $goodsSkuBus->getNormalInIds($skuIds);
        $skuPrice = array_column($skus,"price","id");

//        获取商品sku
        $specsValuIds = array_column($skus,"specs_value_ids","id");
        $specsValueBus = new SpecsValue();
        $specsValues = $specsValueBus->dealSpecsValues($specsValuIds);

        $result = [];
        foreach ($allCart as $k => $cart){
            $cart = json_decode($cart,true);
            $cart['id'] = $k;
            $cart['image'] = preg_match('/http:\/\//',$cart['image']) ? $cart['image'] : "http://localhost".$cart['image'];
            $cart['price'] = $skuPrice[$k] ?? 0.00;
            $cart['sku'] = $specsValues[$k] ?? "暂无规格";
            $result[] = $cart;
        }
//        根据加入购物车时间倒序
        if ($result){
            $result = Arr::sortArr($result,"create_time");
        }
        return $result;
    }

    /**
     * 删除购物车商品
     * @param $userId
     * @param $id
     * @return bool
     */
    public function delete($userId,$id){
        try {
            $result = Cache::hDel(Key::cartKey($userId),$id);
        }catch (Exception $e){
            return FALSE;
        }
        return $result;
    }

    /**
     * 更新购物车商品数量
     * @param $userId
     * @param $id
     * @param $num
     * @return bool
     * @throws Exception
     */
    public function update($userId,$id,$num){
        try {
            $cart = Cache::hGet(Key::cartKey($userId),$id);
        }catch (Exception $e){
            return FALSE;
        }
        if (!$cart){
            throw new Exception("购物车商品不存在");
        }
        try {
            $cart = json_decode($cart,true);
            $cart['num'] = $num;
            Cache::hSet(Key::cartKey($userId),$id,json_encode($cart));
        }catch (Exception $e){
            return FALSE;
        }
        return true;
    }

    /**
     * 获取用户购物车数量
     * @param $userId
     * @return int
     */
    public function getCartCount($userId){
        try {
            $count = Cache::hLen(Key::cartKey($userId));
        }catch (Exception $e){
            return 0;
        }
        return $count;
    }
}