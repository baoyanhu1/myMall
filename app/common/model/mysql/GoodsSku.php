<?php


namespace app\common\model\mysql;

class GoodsSku extends BaseModel
{
    /**
     * goods_sku表联查goods表
     * @return \think\model\relation\HasOne
     */
    public function goods(){
//        第一个参数关联模型名，第二个参数为关联模型的外键，第三个参数为当前模型的主键
        return $this->hasOne(Goods::class,"id","goods_id");
    }

    /**
     * 按商品Id获取所有sku数据
     * @param $goodsId
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSkusByGoodsId($goodsId){
        $where = [
            "goods_id" => $goodsId,
            "status" => config("status.mysql.table_normal")
        ];
        $result = $this->where($where)->select();
        return $result;
    }

}