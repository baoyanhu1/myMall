<?php


namespace app\api\validate;


use think\Validate;

class Cart extends Validate
{
    protected $rule = [
        "id" => "require|integer",
        "num" => "require|integer|gt:0"
    ];

    protected $message = [
        "id.require" => "商品ID不能为空",
        "id.integer" => "商品ID类型错误",
        "num.require" => "添加购物车数量不能为空",
        "num.integer" => "添加购物车数量类型错误",
        "num.gt" => "添加购物车数量最小为1",
    ];

    protected $scene = [
        "add" => ["id","num"],
        "del" => ["id"],
        "update" => ["id","num"]
    ];
}