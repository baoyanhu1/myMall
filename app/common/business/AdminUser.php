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
        $data = [
            "username" => $data['username'],
            "password" => phpass($data['password']),
            "status" => config('status.mysql.table_normal'),
            "create_time" => time(),
            "update_time" => time(),
            "last_login_time" => "",
            "last_login_ip" => "",
            "operate_user" => $isUser
        ];
        try {
            return $this->model->setSave($data);
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

    /**
     * 根据主键ID查询管理员名称
     * @param $id
     * @return array|\think\Model|null
     */
   public function getAdminUserById($id)
   {
       try {
           return $this->model->getAdminUserById($id);
       }catch (Exception $e){
           return [];
       }
   }

   /**
    * 编辑管理员信息
     * @param $id
     * @param $data
     * @param $isUser
     */
   public function edit($id,$data,$isUser)
   {
       $data = [
           "password" => phpass($data),
           "update_time" => time(),
           "operate_user" => $isUser
       ];
       try {
           return $this->model->updateById($id,$data);
       }catch (Exception $e){
           return [];
       }
   }

    /**
     * 获得角色名称列表
     * @return array
     * @throws \think\db\exception\DbException
     */
   public function getRole()
   {
       $roleBus = new AdminRole();
       return $roleBus->getRoleInfo(999);
   }

    /**
     * 给管理员绑定角色
     * 添加角色信息
     * @param $data
     * @param $isUser
     */
    public function setUserRole($data,$isUser)
    {
        $adminUserRole = new AdminUserRole();
        $setRole = $adminUserRole->setRole();
    }

    /**
     * 根据userID查询管理员是否拥有权限
     * @param $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getDuplicateData($data)
    {
        $adminUserRole = new AdminUserRole();
        return $adminUserRole->getAdminRoleRuleByUserId($data['id'])->toArray();
    }
}
