<?php


namespace app\api\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        "ids" => "require",
        "address_id" => "require|number",
        "id" => "require"
    ];
    protected $message = [
        "ids" => "没有选择购买的商品",
        "address_id.require" => "收货地址必选",
        "address_id.number" => "收货地址类型错误",
        "id" => "没有提交的订单"
    ];
    protected $scene = [
        "save" => ["ids","address_id"],
        "read" => ["id"]
    ];
}