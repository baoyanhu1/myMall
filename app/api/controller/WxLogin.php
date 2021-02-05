<?php

namespace app\api\controller;

use app\BaseController;

class WxLogin extends BaseController
{
    /**
     * 登录成功，微信用户信息
     * @param $token
     * @return array
     */
    public function index($token){
        $weixin   = \ThinkSDK\ThinkOauth::getInstance('Weixin', $token);
        $data = $weixin->call('sns/userinfo');
        if($data['ret'] == 0){
            $userInfo['type'] = 'WEIXIN';
            $userInfo['name'] = $data['nickname'];
            $userInfo['nick'] = $data['nickname'];
            $userInfo['head'] = $data['headimgurl'];
            return $userInfo;
        } else {
            throw_exception("获取微信用户信息失败：{$data['errmsg']}");
        }
    }

}