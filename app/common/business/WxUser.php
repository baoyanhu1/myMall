<?php
namespace app\common\business;

use app\common\lib\Str;
use app\common\lib\Time;
use think\Exception;

class WxUser extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\WxUser();
    }

    /**
     * 获取用户基本信息 并保存数据库
     * @param $code
     */
    public function getCodeAndToken($code)
    {
        $AppID = config("status.THINK_SDK_WEIXIN.APP_KEY");
        $AppSecret = config("status.THINK_SDK_WEIXIN.APP_SECRET");
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$code.'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
//得到 access_token 与 openid
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
//得到 用户资料
        return $arr;
    }


    /**
     * 验证openid是否存在
     * @param $data
     * @return array|bool|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function verificationInfo($data)
    {
        $UserInfo = $this->model->verificationInfo($data['openid']);
        //        没有用户信息则新建用户数据
        try {
            if (empty($UserInfo))
            {
                $info = [
                    "wx_name" => $data['nickname'],
                    "xt_name" => "微信用户".time(),
                    "sex" => $data['sex'],
                    "city" => $data['city'],
                    "province" => $data['province'],
                    "country" => $data['country'],
                    "language" => $data['language'],
                    "openid" => $data['openid'],
                    "create_time" => time(),
                    "update_time" => time(),
                    "status" => config("status.mysql.table_normal"),
                    "type" => "2"
                ];

                $this->model->saveWxUser($info);
                $user_id = $this->model->id;
                $username = $this->model->wx_name;
                $type = $this->model->type;

            }else
            {
//            存在用户数据则更新数据记录
                $updateData = [
                    "update_time" => time(),
                ];

                $this->model->updateWxUser($data['openid'],$updateData);
                $user_id = $this->model->id;
                $username = $this->model->wx_name;
                $type = $this->model->type;

            }

        }catch (Exception $e){
            throw new Exception("数据库内部异常");
        }

        $token = Str::getLoginToken($data['openid']);
        $token_data = [
            "user_id" => $user_id,
            "username" => $username
        ];
        $res = cache(config("redis.token").$token,$token_data,Time::tokenDataExpireTime($type));
        return $res ? ['token' => $token,'username'=>$username] : false;

    }

}

