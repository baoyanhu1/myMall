<?php

namespace app\api\controller;

use app\BaseController;

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

    }


    /**
     * 获得code
     */
    public function getCode(){
//获取到code
        $code = $_GET["code"];
        $AppID = 'wx0e685b6c2ed0ba7c';
        $AppSecret = '9a280145f59cbf70597f0dbdee980461';
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
//得到 access_token 与 openid
        dump($arr);die();
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
//得到 用户资料
        dump($arr);die();
        echo $echoStr;
        exit;
    }


}