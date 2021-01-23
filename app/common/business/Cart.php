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
//                判断当前sku库存是否不足
                if ($goodsSku['stock'] < $data['num']){
                    throw new Exception($goodsSku['goods']['title']."商品库存不足");
                }
            }
//            将购物车数据存入Redis
            $result = Cache::hSet(Key::cartKey($userId),$id,json_encode($data));
        }catch (Exception $e){
            throw new Exception($e->getMessage());
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

//        获取商品库存
        $stock = array_column($skus,"stock","id");

        $result = [];
        foreach ($allCart as $k => $cart){
            $cart = json_decode($cart,true);
            if ($ids && isset($stock[$k]) && $stock[$k] < $cart['num']){
                throw new Exception($cart['title']."商品库存不足");
            }
            $price = $skuPrice[$k] ?? 0.00;
            $cart['id'] = $k;
//            $cart['image'] = $cart['image'];
            $cart['price'] = $price;
            $cart['total_price'] = $price * $cart['num'];
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
    public function delete($userId,$ids){
        if (!is_array($ids)){
            $ids = explode(",",$ids);
        }
        try {
//            ...为php可变参数（例：传入的数组为[1,2,3]使用...后为1,2,3传入）
            $result = Cache::hDel(Key::cartKey($userId),...$ids);
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
//            获取Redis内商品sku数据
            $cart = Cache::hGet(Key::cartKey($userId),$id);
//            获取商品sku数据（主要获取库存判断库存量是否不足）
            $goodsSkuBus = new GoodsSku();
            $goodsSku = $goodsSkuBus->getGoodsDetailBySkuId($id);
        }catch (Exception $e){
            return FALSE;
        }
        if (!$cart){
            throw new Exception("购物车商品不存在");
        }
        try {
            $cart = json_decode($cart,true);
//            判断库存是否不足      并且判断如果是加购物车操作
            if ($goodsSku['stock'] < $num && $cart['num'] < $num){
                throw new Exception($goodsSku['goods']['title']."商品库存不足");
            }
//            修改Redis内缓存商品数量
            $cart['num'] = $num;
            Cache::hSet(Key::cartKey($userId),$id,json_encode($cart));
        }catch (Exception $e){
            throw new Exception($e->getMessage());
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