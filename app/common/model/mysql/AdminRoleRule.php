<?php
namespace app\common\model\mysql;


use think\Model;

class AdminRoleRule extends BaseModel
{
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
        $where = [
          "id" => $id
        ];
        return $this->where($where)->find();
    }

    /**
     * 更新角色权限
     * @param $info
     */
    public function updateRoleRule($info)
    {
        $where = [
            "role_id" => $info['role_id']
        ];
        return $this->where($where)->update($info);
    }

    /**
     * 添加角色权限
     * @param $info
     */
    public function saveRoleRule($info)
    {
        return $this->insert($info);
    }
}