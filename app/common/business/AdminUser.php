<?php
namespace app\common\business;

use app\common\model\mysql\AdminUser as AdminUserModel;
use think\Exception;

class AdminUser extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new AdminUserModel();
    }
    /**
     * 用户信息
     * @param $num
     * @return array
     */
    public function getUserInfo($num)
    {
        try {
            $info = $this->model->getUserInfo($num);
        }catch (Exception $e){
            return [];
        }
        $toArray = $info->toArray();
        return $toArray;
    }
}
