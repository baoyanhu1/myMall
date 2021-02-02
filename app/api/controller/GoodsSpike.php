<?php


namespace app\api\controller;

use app\common\lib\Show;
use app\common\lib\Snowflake;
use think\facade\Cache;
use app\api\controller\rabbitmq\Publisher;
use app\api\validate\GoodsSpike as GoodsSpikeVil;

class GoodsSpike extends AuthBase
{
    /**
     * 商品秒杀
     * @return \think\response\Json
     */
    public function index(){
        if (!$this->request->isPost()){
            return Show::error([],"请求方式错误");
        }
        $skuId = input("param.id","","intval");
        $addressId = input("param.address_id","","intval");
        $data = [
            "id" => $skuId,
            "address_id" => $addressId,
        ];
        $goodsSpikeVil = new GoodsSpikeVil();
//        验证参数
        $check = $goodsSpikeVil->scene("spike")->check($data);
        if (!$check){
            return Show::error([],$goodsSpikeVil->getError());
        }

//        获取redis内存储数据信息
        $goodsSkipeInfo = Cache::hGet("goods-spike",$skuId);

//        先判断缓存内是否有当前商品
        if (!$goodsSkipeInfo){
            return Show::success([],"当前商品秒杀活动已结束");
        }
        $goodsSkipeInfo = json_decode($goodsSkipeInfo,true);
//        dump($goodsSkipeInfo);
//        判断当前秒杀商品活动是否已开始
//        当前时间
        $current_time = time();
//        dump($current_time);
        if ($goodsSkipeInfo['start_time'] > $current_time){
            return Show::error([],"秒杀活动尚未开始");
        }

//        判断当前秒杀商品活动是否已结束
        if ($goodsSkipeInfo['end_time'] < $current_time){
            return Show::error([],"秒杀活动已结束");
        }

//        判断当前sku商品库存是否足够
        if ($goodsSkipeInfo['stock'] < 1){
            return Show::error([],"当前商品已全部被秒杀,请稍后重试");
        }

//        获取已抢购商品的用户
        $goodsSkipeUsers = Cache::sMembers("goods-spike-user");
//        判断当前用户是否已抢购过商品，每个用户只能抢购一次
        if (in_array($this->user_id,$goodsSkipeUsers)){
            return Show::error([],"每个用户限秒杀一次");
        }

//        以上条件全部通过则减少redis库存
        $goodsSkipeInfo['stock'] = $goodsSkipeInfo['stock'] - 1;
        $redisGoods = Cache::hSet("goods-spike",$skuId,json_encode($goodsSkipeInfo));

//        如果库存减少成功则将用户ID存入redis(用于每个用户只能抢购一次)
        if ($redisGoods !== FALSE){
            Cache::sAdd("goods-spike-user",$this->user_id);
        }

//        实例化生产者并发送消息
        $publisherObj = new Publisher();
        $publisherData = $goodsSkipeInfo;
        $publisherData['user_id'] = $this->user_id;
        $publisherData['address_id'] = $addressId;

        //        生成订单号（使用雪花算法IDWorker）
        $workId = rand(1,1023);
        $orderId = Snowflake::getInstance()->setWorkId($workId)->nextId();
        $orderId = (string) $orderId;
        $publisherData['order_id'] = $orderId;
        $publisherObj::pushMessage(json_encode($publisherData));

        return Show::success(['id' => $orderId],"秒杀成功");
    }
}