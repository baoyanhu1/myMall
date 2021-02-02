<?php


namespace app\api\validate;


use think\Validate;

class GoodsSpike extends Validate
{
    protected $rule = [
        "id" => "require|number",
    ];

    protected $message = [
        "id.require" => "秒杀商品必选",
        "id.number" => "秒杀商品类型错误",
    ];

    protected $scene = [
        "spike" => ['id'],
    ];
}