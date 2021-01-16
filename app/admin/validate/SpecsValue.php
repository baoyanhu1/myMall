<?php


namespace app\admin\validate;


use think\Validate;

class SpecsValue extends Validate
{
    protected $rule = [
        "id" => "require|integer",
        "specs_id" => "require|integer",
        "name" => "require"
    ];

    protected $message = [
        "id.require" => "规格属性id必选",
        "id.integer" => "规格属性id数值类型错误",
        "specs_id.require" => "规格id必选",
        "specs_id.integer" => "规格id数值类型错误",
        "name" => "规格属性名称必填",
    ];
    protected $scene = [
        "getSpecs" => ["specs_id"],
        "save" => ["specs_id","name"],
        "del" => ["id"]
    ];
}