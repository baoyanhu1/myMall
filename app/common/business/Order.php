<?php


namespace app\common\business;

use app\api\controller\rabbitmq\CheckOrderPublisher;
use app\common\model\mysql\Order as OrderModel;
use app\common\lib\Snowflake;
use think\Exception;
use think\facade\Cache;

class Order extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new OrderModel();
    }

    /**
     * 创建订单
     * @param $data
     * @return array|bool
     * @throws Exception
     */
    public function saveOrder($data){
//        生成订单号（使用雪花算法IDWorker）
        $workId = rand(1,1023);
        $orderId = Snowflake::getInstance()->setWorkId($workId)->nextId();
        $orderId = (string) $orderId;

//        根据ids获取购物车内商品
        $cartObj = new Cart();
        $cartGoods = $cartObj->Lists($data['user_id'],$data['ids']);
        if (!$cartGoods){
            return false;
        }

//        插入order_goods表的数据
        $orderGoodsData = array_map(function ($goods) use ($orderId){
            $goods['sku_id'] = $goods['id'];
            unset($goods['id']);
            $goods['order_id'] = $orderId;
            return $goods;
        },$cartGoods);

//        算出当前订单总价
        $totalPrice = array_sum(array_column($cartGoods,"total_price"));
//        插入order表的数据
        $orderData = [
            "user_id" => $data['user_id'],
            "order_id" => $orderId,
            "total_price" => $totalPrice,
            "address_id" => $data['address_id']
        ];

//        开启事务
        $this->model->startTrans();
        try {
            //        插入订单主表order
            $orderResult = $this->save($orderData);
//            dump($orderResult);
//            die();
            if (!$orderResult){
                return false;
            }

            //        插入订单附表order_goods
            $orderGoodsBus = new OrderGoods();
            $orderGoodsBus->saveAll($orderGoodsData);

            //        更新商品sku表库存goods_sku
            $goodsSkuBus = new GoodsSku();
            $numData = array_column($cartGoods,"num","id");
            $goodsSkuStock = $goodsSkuBus->updateStock($numData);
            if (!$goodsSkuStock){
                return false;
            }

            //        更新商品主表goods库存
//            获取所有商品sku数据
            $goodsSkuIds = $goodsSkuBus->getNormalInIds(explode(",",$data['ids']));
            $goodsIds = array_unique(array_column($goodsSkuIds,"goods_id"));
//            获取sku表当前商品所有sku总库存
            $sumStock = $goodsSkuBus->sumStock($goodsIds);
            $goodsBus = new Goods();
            $goodsStock = $goodsBus->updateStock($sumStock);
            if (!$goodsStock){
                return false;
            }

            //        删除购物车商品Redis
            $deleteCart = $cartObj->delete($data['user_id'],$data['ids']);
            if (!$deleteCart){
                return false;
            }
            $result = [
                "id" => $orderId
            ];
//            事务提交
            $this->model->commit();
        }catch (Exception $e){
//            事务回滚
            $this->model->rollback();
            return false;
        }
        return $result;
    }

    /**
     * 创建秒杀订单
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function saveSpikeOrder($data){
        $spikeData = json_decode($data,true);
        //        生成订单号（使用雪花算法IDWorker）
        $workId = rand(1,1023);
        $orderId = Snowflake::getInstance()->setWorkId($workId)->nextId();
        $orderId = (string) $orderId;

//        插入order表的数据
        $orderData = [
            "user_id" => $spikeData['user_id'],
            "order_id" => $orderId,
            "total_price" => $spikeData['price'],
            "address_id" => $spikeData['address_id'],
            "is_spike" => config("status.mysql.table_normal")
        ];

        //        插入order_goods表的数据
        $specsValueBus = new SpecsValue();
        $specsValues = $specsValueBus->dealSpecsValues(explode(",",$spikeData['specs_value_ids']));

        $orderGoodsData = [
            "order_id" => $orderId,
            "sku_id" => $spikeData['sku_id'],
            "sku" => implode(" ",$specsValues),
            "goods_id" => $spikeData['goods_id'],
            "num" => config("status.mysql.table_normal"),
            "price" => $spikeData['price'],
            "title" => $spikeData['title'],
            "image" => $spikeData['image'],
        ];

//        开启事务
        $this->model->startTrans();
        try {
            //        插入订单主表order
            $orderResult = $this->save($orderData);

            //        插入订单附表order_goods
            $orderGoodsBus = new OrderGoods();
            $orderGoodsResult = $orderGoodsBus->save($orderGoodsData);

//            订单主表和附表全部新增成功
            if ($orderResult && $orderGoodsResult){
                $publisherData = [
                    "user_id" => $spikeData['user_id'],
                    "order_id" => $orderId,
                    "sku_id" => $spikeData['sku_id'],
                ];
                $checkOrderPublisherObj = new CheckOrderPublisher();
                $checkOrderPublisherObj::pushMessage(json_encode($publisherData));
            }
            //            事务提交
            $this->model->commit();
        }catch (Exception $e){
//            事务回滚
            $this->model->rollback();
            return false;
        }
    }

    /**
     * 检查秒杀商品是否支付
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkOrderStatus($data){
        $data = json_decode($data,true);

        //获取订单信息
        $orderInfo = $this->model->getOrderStatus($data['order_id']);
        $orderInfo = $orderInfo->toArray();
        //如果订单为未支付状态（将订单状态改为已取消、删除redis内存储的用户ID（用来 用户可以再次发起秒杀）、根据接收到的商品ID自增Redis内商品库存）
        if ($orderInfo['status'] == config('status.mysql.pending_payment')){
            try {
                //更改订单状态为取消状态
                $ordeResult = $this->model->updateOrderStatus($data['order_id'],config('status.mysql.is_cancelled'));
                if ($ordeResult){
                    //删除redis内存储的用户ID（用来 用户可以再次发起秒杀）
                    Cache::sRem('goods-spike-user',$data['user_id']);
//                    根据接收到的商品skuID增加Redis内商品库存
//                    获取redis内数据
                    $goodsSkipeInfo = Cache::hGet("goods-spike",$data['sku_id']);
                    if ($goodsSkipeInfo){
                        $goodsSkipeInfo = json_decode($goodsSkipeInfo,true);
                        $goodsSkipeInfo['stock'] = $goodsSkipeInfo['stock'] + 1;
                        Cache::hSet("goods-spike",$data['sku_id'],json_encode($goodsSkipeInfo));
                    }
                }
            }catch (Exception $e){
//                todo 记录日志
            }
        }
    }

    /**
     * 获取订单详细信息
     * @param $orderData
     * @return array|mixed
     */
    public function orderDetail($orderData){
        $condition = [
            "order_id" => $orderData["id"],
            "user_id" => $orderData["user_id"]
        ];
        try {
            $order = $this->model->getByCondition($condition);
        }catch (Exception $e){
            return [];
        }
        if (!$order){
            return [];
        }
        $order = $order->toArray()[0];
//        获取用户收货地址
        $addressId = $order['address_id'];
        $receiptAddressBus = new ReceiptAddress();
        $address = $receiptAddressBus->getReceiptAddress($addressId);

//        获取用户详细收货地址
        if ($address){
            $address = $address[0];
            $areaIds = $address['area'];
            $addressBus = new Address();
            $area = $addressBus->getArea($areaIds);
            $detailArea = $addressBus->getStrArea($area);
            $detailAreaStr = $detailArea['consignee_info'].$address['detail_address']."  ".$address['receiver_name']."  ".$address['phone'];
        }else{
            $detailAreaStr = "";
        }

//        获取order_goods表订单数据
        $orderGoodsBus = new OrderGoods();
        $orderGoods = $orderGoodsBus->getOrderGoodsByOrderId($orderData["id"]);

//        组装最终返回数据
        $order['id'] = $order['order_id'];
        $order['consignee_info'] = $detailAreaStr;
        $order['malls'] = $orderGoods;
        return $order;
    }
}