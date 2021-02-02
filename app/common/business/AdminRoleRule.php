<?php
namespace app\common\business;

use think\Exception;

class AdminRoleRule extends BusBase
{
    public $model;
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\AdminRoleRule();
    }
    /**
     * 根据role_id查询角色下权限
     * @param $id
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getRoleRule($id)
    {
        try {
            return $this->model->getRoleRule($id);
        }catch (Exception $e){
            return [];
        }

    }

    /**
     * 保存角色权限
     * @param $empty
     * @param $data
     * @param $isUser
     * @return array
     */
    public function roleRuleSave($empty,$data,$isUser)
    {
        $info = [
            "role_id" => $data['id'],
            "rule_ids" => implode(',',$data['onArray']),
            "update_time" => time(),
            "operate_user" => $isUser,
            "status" => config("status.mysql.table_normal")
        ];

        if (!empty($empty))
        {
            try {
                return $this->model->updateRoleRule($info);
            }catch (Exception $e){
                return [];
            }
        }

        $info += [
            "create_time" => time(),
        ];

        try {
            return $this->model->saveRoleRule($info);
        }catch (Exception $e){
            return [];
        }
    }
}