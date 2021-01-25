<?php
namespace app\common\business;

use think\Exception;

class AdminRole extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\AdminRole();
    }



    /**
     * 获取角色信息
     * @param $num
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function getRoleInfo($num)
    {
        try {
            $info = $this->model->getRoleInfo($num);
        }catch (Exception $e){
            return [];
        }
        return $info->toArray();
    }

    /**
     * 通过角色名称查询单条数据
     * @param $name
     * @return array|false|\think\Model|null
     */
    public function getAdminRoleByName($name)
    {
        try {
            return $this->model->getAdminRoleByName($name);
        }catch (Exception $e){
            return [];
        }

    }

    /**
     * 角色信息提交并保存
     * @param $data
     * @param $isUser
     * @return array|int|string
     */
    public function setSave($data,$isUser)
    {
        $data = [
            "name" => $data['name'],
            "create_time" => time(),
            "update_time" => time(),
            "operate_user" => $isUser,
            "status" => config('status.mysql.table_normal')
        ];

        try {
            return $this->model->setSave($data);
        }catch (Exception $e){
            return [];
        }
    }
}