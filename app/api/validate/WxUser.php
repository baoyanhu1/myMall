<?php
namespace app\api\validate;


use think\Validate;

class WxUser extends Validate
{
    protected $rule = [
        "code" => "require",
    ];

    // code 验证场景定义
    public function sceneCode()
    {
        return $this->only(['code']);
    }
}