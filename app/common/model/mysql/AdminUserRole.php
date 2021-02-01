<?php
namespace app\common\model\mysql;


use think\Model;

class AdminUserRole extends BaseModel
{

    /**
     * 更新管理员角色
     * @param $info
     * @return bool
     */
    public function updateRole($id,$info)
    {
        return $this->updateById($id,$info);
    }

    /**
     * 添加管理员角色
     * @param $info
     * @return int|string
     */
    public function insertRole($info)
    {
        return $this->insert($info);
    }

    /**
     * 根据userID查询管理员角色
     * @param $id
     * @return array|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminRoleRuleByUserId($id)
    {
        $where = [
            "user_id" => $id
        ];
        return $this->where($where)->find();
    }

}