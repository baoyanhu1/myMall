<?php


namespace app\admin\validate;


use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        'src' => 'require'
    ];

    protected $message = [
        'src' => '删除图片地址不存在'
    ];
    protected $scene = [
        'deleteImage' => ['src']
    ];
}