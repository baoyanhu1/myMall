<?php
namespace app\common\model\mysql;


use think\Model;

class AdminRole extends BaseModel
{
    /**
     * 关联AdminRole表
     * @return AdminRole|void
     */
    public function AdminRole()
    {
        return $this->hasOne(AdminRole::class,'role_id');
    }

    /**
     * 获得角色信息
     * @param $num
     * @param string $status
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public function getRoleInfo($num,$status = "")
    {
        $statusNormal = !empty($status) ? $status : config("status.mysql.table_normal");
        $statusPending = !empty($status) ? $status : config("status.mysql.table_pending");
        $where = [
            'status' => [$statusNormal,$statusPending]
        ];
        $order = [
            'id' => 'ASC'
        ];
        return $this->where($where)
                ->order($order)
                ->paginate($num);
    }

    /**
     * 通过角色名称查询当前数据
     * @param $name
     * @return array|false|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminRoleByName($name)
    {
        if (empty($name)){
            return false;
        }
        $where = [
            "name" => $name
        ];
        return $this->where($where)->find();
    }

    /**
     * 添加角色信息并保存
     * @param $data
     * @return int|string
     */
    public function setSave($data)
    {
        return $this->insert($data);
    }
}