<?php
namespace app\common\model\mysql;


use think\Model;

class AdminUserRole extends BaseModel
{
    /**
     * 给管理员绑定角色
     * 添加角色信息
     * @param $data
     * @return int|string
     */
    public function setRole($data)
    {
        return $this->insert($data);
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