<?php


namespace app\api\validate;


use think\Validate;

class GoodsSpike extends Validate
{
    protected $rule = [
        "id" => "require|number",
        "address_id" => "require"
    ];

    protected $message = [
        "id.require" => "秒杀商品必选",
        "id.number" => "秒杀商品类型错误",
        "address_id" => "收货地址必选"
    ];

    protected $scene = [
        "spike" => ['id','address_id'],
    ];
}