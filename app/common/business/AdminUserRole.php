<?php
namespace app\common\business;


use think\Exception;

class AdminUserRole extends BusBase
{

    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\AdminUserRole();
    }

    /**
     * 给管理员绑定角色
     * 添加角色信息
     * @param $data
     * @param $isUser
     * @return array|int|string
     */
    public function setRole($data,$isUser)
    {
        $info = [
            "user_id" => $data['id'],
            "role_id" => $data['roleid'],
            "create_time" => time(),
            "operate_user" => $isUser,
            "status" => "1",
        ];
        try {
            //根据userID查询管理员角色
            $adminUser = $this->getAdminRoleRuleByUserId($data['id']);
            if (!empty($adminUser))
            {
                //更新角色信息
                return $this->model->updateRole($data['id'],$info);
            }
            //添加角色信息
            return $this->model->insertRole($info);
        }catch (Exception $e){
            return [];
        }

    }


    /**
     * 根据userID查询管理员角色
     * @param $id
     * @return array|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminRoleRuleByUserId($id)
    {
        try {
            return $this->model->getAdminRoleRuleByUserId($id);
        }catch (Exception $e){
            return [];
        }

    }
}
