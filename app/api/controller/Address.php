<?php


namespace app\api\controller;
use app\common\business\Address as AddressBus;
use app\common\lib\Show;

class Address extends AuthBase
{
    /**
     * 获取用户收货地址
     * @return \think\response\Json
     */
    public function index(){
        $addressBus = new AddressBus();
        $result = $addressBus->getReceiptAddress($this->user_id);
        return Show::success($result);
    }
}