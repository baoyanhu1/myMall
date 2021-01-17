<?php


namespace app\api\validate;


use think\Validate;

class Category extends Validate
{
    protected $rule = [
        'id' => 'require|integer',
    ];

    protected $message = [
       'id.require' => 'id不能为空',
       'id.integer' => 'id类型错误',
    ];
    protected $scene = [
        'search' => ['id'],
    ];
}