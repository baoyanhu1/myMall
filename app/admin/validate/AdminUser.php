<?php


namespace app\admin\validate;


use think\Validate;

class AdminUser extends Validate
{
    protected $rule = [
        "username" => "require",
        "password" => "require",
        "password_confirm" => "require",
//        "captcha" => "require|checkCaptcha",
        "id" => "require",
        "status" => "require",
    ];

    protected $message = [
        "username.require" => "用户名不能为空",
        "password.require" => "密码不能为空",
        "password_confirm.require" => '再次输入密码不能为空',
        "password.confirm" => '两次密码不一致',
        "password.min" => '请输入不小于6位密码',
//        "captcha" => "验证码必填",
    ];

    public function checkCaptcha($value){
        if (!captcha_check($value)){
            return "验证码错误";
        }
        return true;
    }

    // login 验证场景定义
    public function sceneLogin()
    {
        return $this->only(['username','password']);
    }

    // user 验证场景定义
    public function sceneUser()
    {
        return $this->only(['username','password','password_confirm'])
            ->append("password",'confirm')
            ->append("password",'min:6');
    }

    // status 验证场景定义
    public function sceneStatus()
    {
        return $this->only(['id','status']);
    }

    // password 验证场景定义
    public function scenePassword()
    {
        return $this->only(['id','password','password_confirm'])
            ->append("password",'confirm')
            ->append("password",'min:6');
    }

    // id 验证场景定义
    public function sceneId()
    {
        return $this->only(['id']);
    }

}