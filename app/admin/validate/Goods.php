<?php


namespace app\admin\validate;


use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'src' => 'require',
        'id' => 'require|integer',
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'src' => '删除图片地址不存在',
        'id.require' => 'id不能为空',
        'id.integer' => 'id类型错误',
        'status.require' => '状态值不能为空',
        'status.in' => '状态数值范围不合法'
    ];
    protected $scene = [
        'deleteImage' => ['src'],
        'changeStatus' => ['id','status'],
    ];
}