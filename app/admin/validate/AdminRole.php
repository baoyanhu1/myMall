<?php
namespace app\admin\validate;

use think\Validate;

class AdminRole extends Validate
{
    protected $rule = [
        "name" => "require",
    ];

    protected $message = [
        "name.require" => "用户名不能为空",
    ];

    // Role 验证场景定义
    public function sceneRole()
    {
        return $this->only(['name']);
    }
}