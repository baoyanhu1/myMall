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

    /**
     * 提交用户信息
     * @param $data
     * @param $isUser
     */
    public function setSave($data,$isUser)
    {
        $pass = phpass($data['password']);
        try {
            return $this->model->setSave($data['username'],$pass,$isUser);
        }catch (Exception $e){
            return [];
        }

    }

    /**
     * 根据用户名获取后台管理用户信息
     * @param $username
     * @return array|bool|\think\Model|null
     */
    public function getAdminUserByUserName($username)
    {
        try {
            return $this->model->getAdminUserByUserName($username);
        }catch (Exception $e){
            return [];
        }
    }

    /**
     * 更改管理员状态
     * @param $data
     * @param $id
     */
   public function changeStatus($id,$data)
   {
       try {
           return $this->model->updateById($id,$data);
       }catch (Exception $e){
           return [];
       }
   }
}
