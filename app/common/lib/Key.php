<?php


namespace app\common\lib;


class Key
{
    /**
     * 添加购物车Redis使用的Key
     * @param $userId
     * @return mixed
     */
    public static function cartKey($userId){
        return config("redis.mall_cart_user").$userId;
    }
}