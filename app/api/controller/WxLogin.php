<?php

namespace app\api\controller;

use app\BaseController;
use app\common\business\WxUser;
use dh2y\qrcode\QRcode;

class WxLogin extends BaseController
{

    /**
     * 显示微信登录二维码
     */
    public function index()
    {
//配置
        $AppID = config("status.THINK_SDK_WEIXIN.APP_KEY");
        $callback  =  config("status.THINK_SDK_WEIXIN.CALLBACK"); //回调地址
//微信登录
        session_start();
//生成唯一随机串防CSRF攻击
        $state  = md5(uniqid(rand(), TRUE));
        $_SESSION["wx_state"]    =   $state; //存到SESSION
        $callback = urlencode($callback);
        $wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state={$state}&connect_redirect=1#wechat_redirect";
        //$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
//生成二维码返回
        $png = new QRcode();
        return $png->png($wxurl)->show();
    }


    /**
     * 获得code
     */
    public function getCode(){
        //获取到code
        $code = $_GET["code"];
        $data = [
          "code" =>  $code
        ];
        //参数验证
        $vilidateObj = new \app\api\validate\WxUser();
        $vilidate = $vilidateObj->scene('code')->check($data);
        if (!$vilidate){
            return show(config('status.error'),$vilidateObj->getError());
        }
        //        到业务逻辑
        try {

            $modelObj = new WxUser();
            $WxUserInfo = $modelObj->getCodeAndToken($code);
            //验证是否有该用户
            $DuplicateInfo = $modelObj->verificationInfo($WxUserInfo);

        }catch (Exception $e){
            return show(config("status.error"),$e->getMessage());
        }

        if ($DuplicateInfo){
            return show(config("status.success"),"登陆成功",$DuplicateInfo);
        }
        return show(config("status.error"),"登陆失败");

    }


}